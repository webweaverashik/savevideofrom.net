#!/usr/bin/env python3
"""SaveVideoFrom.net — metadata extractor. Tries public first, then platform cookies."""
from __future__ import annotations

import os
import random

from lib.response import read_input, emit_success, emit_error, log
from lib.errors import classify, cookies_might_help
from lib.ytdlp import get_ytdlp, base_opts, normalize_info
from lib.cookies import valid_cookie_file


def attempt(yt_dlp, url: str, ffmpeg_path, cookie_file):
    opts = base_opts(ffmpeg_path=ffmpeg_path, cookies_file=valid_cookie_file(cookie_file))
    opts["skip_download"] = True
    with yt_dlp.YoutubeDL(opts) as ydl:
        return ydl.extract_info(url, download=False)


def main() -> None:
    data = read_input()
    url = (data.get("url") or "").strip()
    if not url:
        emit_error("No URL provided.", "bad_input", retryable=False)

    yt_dlp = get_ytdlp()
    ffmpeg_path = data.get("ffmpeg_path")

    cookies = [c for c in (data.get("cookies_files") or []) if valid_cookie_file(c)]
    random.shuffle(cookies)
    candidates = [None] + cookies  # public first, then each cookie

    last = ("download_error", "Could not process this content.", True)

    for cookie in candidates:
        label = "public" if cookie is None else os.path.basename(cookie)
        try:
            info = attempt(yt_dlp, url, ffmpeg_path, cookie)
        except yt_dlp.utils.DownloadError as e:
            last = classify(str(e))
            log(f"extract [{label}] failed: {e}")
            if not cookies_might_help(last[0]):
                break
            continue
        except Exception as e:  # noqa: BLE001
            last = classify(str(e))
            log(f"extract [{label}] error: {e}")
            if not cookies_might_help(last[0]):
                break
            continue

        normalized = normalize_info(info, platform=data.get("platform"))
        if normalized["formats"]:
            log(f"extract succeeded with [{label}]")
            emit_success(normalized)
        last = ("unsupported", "No downloadable formats were found.", False)
        break

    emit_error(last[1], last[0], last[2])


if __name__ == "__main__":
    main()