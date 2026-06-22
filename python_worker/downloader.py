#!/usr/bin/env python3
"""SaveVideoFrom.net — download worker. Implements the MediaDownloader contract."""
from __future__ import annotations

import os
import re

from lib.response import read_input, emit_success, emit_error, log
from lib.errors import classify
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

    if fmt_id and fmt_id not in ("auto", "best"):
        opts["format"] = f"{fmt_id}+bestaudio/{fmt_id}/best"
    elif quality and quality.lower() not in ("highest", "best"):
        height = re.sub(r"\D", "", quality) or "1080"
        opts["format"] = f"bestvideo[height<={height}]+bestaudio/best[height<={height}]/best"
    else:
        opts["format"] = "bestvideo+bestaudio/best"

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


def main() -> None:
    data = read_input()
    url = (data.get("url") or "").strip()
    output_dir = data.get("output_dir")
    if not url or not output_dir:
        emit_error("Missing url or output_dir.", "bad_input", retryable=False)

    os.makedirs(output_dir, exist_ok=True)

    yt_dlp = get_ytdlp()
    opts = base_opts(
        ffmpeg_path=data.get("ffmpeg_path"),
        cookies_file=valid_cookie_file(data.get("cookies_file")),
    )
    opts["outtmpl"] = os.path.join(output_dir, "%(title).80s.%(ext)s")
    opts["restrictfilenames"] = True
    opts["windowsfilenames"] = True

    max_mb = data.get("max_filesize_mb")
    if max_mb:
        opts["max_filesize"] = int(max_mb) * 1024 * 1024

    build_format_opts(opts, data)

    try:
        with yt_dlp.YoutubeDL(opts) as ydl:
            info = ydl.extract_info(url, download=True)
    except yt_dlp.utils.DownloadError as e:
        etype, msg, retry = classify(str(e))
        log(f"DownloadError: {e}")
        emit_error(msg, etype, retry)
    except Exception as e:  # noqa: BLE001
        etype, msg, retry = classify(str(e))
        log(f"Unexpected: {e}")
        emit_error(msg, etype, retry)

    final_path = resolve_output_file(info, output_dir)
    if not final_path:
        emit_error("Download finished but no output file was produced.", "no_media", retryable=False)

    ext = os.path.splitext(final_path)[1].lstrip(".").lower()
    audio_only = bool(data.get("audio_only")) or data.get("media_type") == "audio"

    emit_success({
        "file_name": os.path.basename(final_path),
        "file_path": final_path,
        "file_size": os.path.getsize(final_path),
        "mime_type": MIME.get(ext, "application/octet-stream"),
        "media_type": "audio" if audio_only else "video",
        "title": (info.get("title") or "download"),
    })


if __name__ == "__main__":
    main()