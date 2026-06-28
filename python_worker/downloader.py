#!/usr/bin/env python3
"""SaveVideoFrom.net — download worker. Tries public first, then platform cookies."""
from __future__ import annotations

import os
import random
import re

from lib.response import read_input, emit_success, emit_error, log
from lib.errors import classify, cookies_might_help
from lib.ytdlp import get_ytdlp, base_opts
from lib.cookies import valid_cookie_file

MIME = {
    "mp4": "video/mp4", "webm": "video/webm", "mkv": "video/x-matroska",
    "mp3": "audio/mpeg", "m4a": "audio/mp4", "aac": "audio/aac", "wav": "audio/wav",
    "jpg": "image/jpeg", "jpeg": "image/jpeg", "png": "image/png", "webp": "image/webp",
}
AUDIO_CODECS = {"mp3", "m4a", "aac", "wav"}
VIDEO_CONTAINERS = {"mp4", "webm", "mkv"}


def build_format_opts(opts: dict, data: dict) -> None:
    media_type = data.get("media_type", "video")
    audio_only = bool(data.get("audio_only")) or media_type == "audio"
    fmt_id = (data.get("format_id") or "").strip()
    quality = (data.get("quality") or "").strip()
    requested = (data.get("requested_format") or "").strip().lower()

    if audio_only:
        opts["format"] = "bestaudio/best"
        codec = requested if requested in AUDIO_CODECS else "mp3"
        opts["postprocessors"] = [{
            "key": "FFmpegExtractAudio",
            "preferredcodec": codec,
            "preferredquality": "192",
        }]
        return

# If we have an explicit format_id, prefer it (merge audio if separate, else take as-is).
    if fmt_id and fmt_id not in ("auto", "best"):
        opts["format"] = f"{fmt_id}+bestaudio/{fmt_id}/best"
    else:
        digits = re.sub(r"\D", "", quality)
        if re.match(r"^\d+p$", quality) and digits:
            h = digits
            opts["format"] = (
                f"bestvideo[height<={h}]+bestaudio/"   # separate streams, merge
                f"best[height<={h}]/"                  # muxed at/under height
                f"bestvideo[height<={h}]/"             # video-only at/under height (no audio exists)
                f"bestvideo+bestaudio/"                # any separate streams
                f"best/"                               # any muxed
                f"bestvideo"                           # any video-only (last resort)
            )
        else:
            opts["format"] = "bestvideo+bestaudio/best/bestvideo/best"

    opts["merge_output_format"] = requested if requested in VIDEO_CONTAINERS else "mp4"

def resolve_output_file(info: dict, output_dir: str) -> str | None:
    rd = info.get("requested_downloads")
    if rd and isinstance(rd, list) and rd[0].get("filepath") and os.path.exists(rd[0]["filepath"]):
        return rd[0]["filepath"]

    candidates = [
        os.path.join(output_dir, f)
        for f in os.listdir(output_dir)
        if os.path.isfile(os.path.join(output_dir, f)) and not f.endswith(".part")
    ]
    return max(candidates, key=os.path.getsize) if candidates else None


def clear_dir(output_dir: str) -> None:
    for f in os.listdir(output_dir):
        p = os.path.join(output_dir, f)
        if os.path.isfile(p):
            try:
                os.remove(p)
            except OSError:
                pass


def attempt_download(yt_dlp, data: dict, output_dir: str, cookie_file):
    opts = base_opts(ffmpeg_path=data.get("ffmpeg_path"), cookies_file=valid_cookie_file(cookie_file))
    opts["outtmpl"] = os.path.join(output_dir, "%(title).80s.%(ext)s")
    opts["restrictfilenames"] = True
    opts["windowsfilenames"] = True

    max_mb = data.get("max_filesize_mb")
    if max_mb:
        opts["max_filesize"] = int(max_mb) * 1024 * 1024

    build_format_opts(opts, data)

    with yt_dlp.YoutubeDL(opts) as ydl:
        return ydl.extract_info(data["url"], download=True)


def main() -> None:
    data = read_input()
    url = (data.get("url") or "").strip()
    output_dir = data.get("output_dir")
    if not url or not output_dir:
        emit_error("Missing url or output_dir.", "bad_input", retryable=False)

    os.makedirs(output_dir, exist_ok=True)

    yt_dlp = get_ytdlp()
    cookies = [c for c in (data.get("cookies_files") or []) if valid_cookie_file(c)]
    random.shuffle(cookies)
    candidates = [None] + cookies

    audio_only = bool(data.get("audio_only")) or data.get("media_type") == "audio"
    last = ("download_error", "Could not process this content.", True)

    for cookie in candidates:
        label = "public" if cookie is None else os.path.basename(cookie)
        clear_dir(output_dir)
        try:
            info = attempt_download(yt_dlp, data, output_dir, cookie)
        except yt_dlp.utils.DownloadError as e:
            last = classify(str(e))
            log(f"download [{label}] failed: {e}")
            if not cookies_might_help(last[0]):
                break
            continue
        except Exception as e:  # noqa: BLE001
            last = classify(str(e))
            log(f"download [{label}] error: {e}")
            if not cookies_might_help(last[0]):
                break
            continue

        final_path = resolve_output_file(info, output_dir)
        if not final_path:
            last = ("no_media", "Download finished but no output file was produced.", False)
            break

        ext = os.path.splitext(final_path)[1].lstrip(".").lower()
        log(f"download succeeded with [{label}]")
        emit_success({
            "file_name": os.path.basename(final_path),
            "file_path": final_path,
            "file_size": os.path.getsize(final_path),
            "mime_type": MIME.get(ext, "application/octet-stream"),
            "media_type": "audio" if audio_only else "video",
            "title": (info.get("title") or "download"),
        })

    emit_error(last[1], last[0], last[2])


if __name__ == "__main__":
    main()