"""Thin wrapper over yt-dlp: import guard, shared options, and format normalization."""
from __future__ import annotations

from lib.response import emit_error


def get_ytdlp():
    try:
        import yt_dlp
        return yt_dlp
    except ImportError:
        emit_error("yt-dlp is not installed in this Python environment.", "ytdlp_missing", retryable=False)


def base_opts(ffmpeg_path: str | None = None, cookies_file: str | None = None) -> dict:
    opts = {
        "quiet": True,
        "no_warnings": True,
        "noprogress": True,
        "ignoreconfig": True,
        "nocheckcertificate": True,
        "noplaylist": True,
        "retries": 3,
        "extractor_retries": 1,
        "skip_download": True,
        "socket_timeout": 30,
        "geo_bypass": True,
        "http_headers": {
            "User-Agent": ("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
                           "(KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"),
        },
        "extractor_args": {"youtube": {"player_client": ["android"]}},
        # "proxy": "http://user:pass@proxy-host:port",
    }
    if ffmpeg_path:
        opts["ffmpeg_location"] = ffmpeg_path
    if cookies_file:
        opts["cookiefile"] = cookies_file
    return opts


def first_entry(info: dict) -> dict:
    """Collapse a playlist to its first entry; single videos pass through."""
    if info.get("entries"):
        entries = [e for e in info["entries"] if e]
        return entries[0] if entries else info
    return info


def _video_options(formats: list[dict]) -> list[dict]:
    by_height: dict[int, tuple[float, dict]] = {}
    for f in formats:
        if f.get("vcodec") in (None, "none"):
            continue
        h = f.get("height")
        if not h:
            continue
        score = float(f.get("tbr") or 0)
        if h not in by_height or score > by_height[h][0]:
            by_height[h] = (score, f)

    options = []
    for h in sorted(by_height, reverse=True):
        f = by_height[h][1]
        has_audio = f.get("acodec") not in (None, "none")
        width = f.get("width")
        options.append({
            "format_id": str(f.get("format_id")),
            "ext": f.get("ext") or "mp4",
            "type": "video",
            "quality": f"{h}p",
            "resolution": f"{width}x{h}" if width else None,
            "filesize": f.get("filesize") or f.get("filesize_approx"),
            "fps": int(f["fps"]) if f.get("fps") else None,
            "vcodec": f.get("vcodec"),
            "acodec": f.get("acodec") if has_audio else None,
            "has_video": True,
            "has_audio": has_audio,
        })
    return options


def _audio_option(formats: list[dict]) -> dict | None:
    best = None
    best_score = -1.0
    for f in formats:
        if f.get("acodec") in (None, "none") or f.get("vcodec") not in (None, "none"):
            continue
        score = float(f.get("abr") or f.get("tbr") or 0)
        if score > best_score:
            best, best_score = f, score
    if not best:
        return None
    abr = best.get("abr")
    return {
        "format_id": str(best.get("format_id")),
        "ext": best.get("ext") or "m4a",
        "type": "audio",
        "quality": f"{int(abr)}kbps" if abr else "Audio",
        "resolution": None,
        "filesize": best.get("filesize") or best.get("filesize_approx"),
        "fps": None,
        "vcodec": None,
        "acodec": best.get("acodec"),
        "has_video": False,
        "has_audio": True,
    }


def normalize_info(info: dict, platform: str | None = None) -> dict:
    entry = first_entry(info)
    raw_formats = entry.get("formats") or []

    formats = _video_options(raw_formats)
    audio = _audio_option(raw_formats)

    # Fallback: site returned video formats with no height (e.g. LinkedIn, some HLS).
    # Treat any non-audio-only format as a selectable video, labeled by bitrate.
    if not formats:
        seen = set()
        muxed = []
        for f in raw_formats:
            vcodec = f.get("vcodec")
            # skip audio-only (has acodec but no video)
            is_audio_only = vcodec in (None, "none") and f.get("acodec") not in (None, "none")
            if is_audio_only:
                continue
            ext = f.get("ext") or "mp4"
            tbr = f.get("tbr") or f.get("vbr") or 0
            key = (ext, round(tbr or 0))
            if key in seen:
                continue
            seen.add(key)
            label = f"{int(tbr)}k" if tbr else (f.get("format_note") or "Video")
            muxed.append({
                "format_id": str(f.get("format_id")),
                "ext": ext,
                "type": "video",
                "quality": label,
                "resolution": None,
                "filesize": f.get("filesize") or f.get("filesize_approx"),
                "fps": int(f["fps"]) if f.get("fps") else None,
                "vcodec": vcodec,
                "acodec": f.get("acodec"),
                "has_video": True,
                "has_audio": f.get("acodec") not in (None, "none"),
            })
        # Highest bitrate first
        muxed.sort(key=lambda x: x["filesize"] or 0, reverse=True)
        formats = muxed

    if audio:
        formats.append(audio)

    # Last resort: single direct URL with no format list.
    if not formats and entry.get("url"):
        formats.append({
            "format_id": str(entry.get("format_id") or "0"),
            "ext": entry.get("ext") or "mp4",
            "type": "video" if entry.get("vcodec") not in (None, "none") else "audio",
            "quality": f"{entry.get('height')}p" if entry.get("height") else "Original",
            "resolution": None,
            "filesize": entry.get("filesize") or entry.get("filesize_approx"),
            "fps": None, "vcodec": entry.get("vcodec"), "acodec": entry.get("acodec"),
            "has_video": entry.get("vcodec") not in (None, "none"),
            "has_audio": entry.get("acodec") not in (None, "none"),
        })

    return {
        "title": entry.get("title") or "Untitled",
        "webpage_url": entry.get("webpage_url") or entry.get("original_url") or "",
        "uploader": entry.get("uploader") or entry.get("channel") or entry.get("uploader_id"),
        "thumbnail": entry.get("thumbnail"),
        "duration": int(entry["duration"]) if entry.get("duration") else None,
        "platform": platform,
        "is_playlist": bool(info.get("entries")),
        "formats": formats,
    }