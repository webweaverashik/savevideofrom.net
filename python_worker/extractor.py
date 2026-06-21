#!/usr/bin/env python3
"""SaveVideoFrom.net — metadata extractor (no download). Implements the MediaExtractor contract."""
from __future__ import annotations

from lib.response import read_input, emit_success, emit_error, log
from lib.errors import classify
from lib.ytdlp import get_ytdlp, base_opts, normalize_info
from lib.cookies import valid_cookie_file


def main() -> None:
    data = read_input()
    url = (data.get("url") or "").strip()
    if not url:
        emit_error("No URL provided.", "bad_input", retryable=False)

    yt_dlp = get_ytdlp()
    opts = base_opts(
        ffmpeg_path=data.get("ffmpeg_path"),
        cookies_file=valid_cookie_file(data.get("cookies_file")),
    )
    opts["skip_download"] = True

    try:
        with yt_dlp.YoutubeDL(opts) as ydl:
            info = ydl.extract_info(url, download=False)
    except yt_dlp.utils.DownloadError as e:
        etype, msg, retry = classify(str(e))
        log(f"DownloadError: {e}")
        emit_error(msg, etype, retry)
    except Exception as e:  # noqa: BLE001
        etype, msg, retry = classify(str(e))
        log(f"Unexpected: {e}")
        emit_error(msg, etype, retry)

    normalized = normalize_info(info, platform=data.get("platform"))
    if not normalized["formats"]:
        emit_error("No downloadable formats were found.", "unsupported", retryable=False)

    emit_success(normalized)


if __name__ == "__main__":
    main()