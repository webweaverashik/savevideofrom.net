#!/usr/bin/env python3
"""SaveVideoFrom.net — playlist lister. Flat-extracts entries (no per-item format probing)."""
from __future__ import annotations

import os
import random

from lib.response import read_input, emit_success, emit_error, log
from lib.errors import classify, cookies_might_help
from lib.ytdlp import get_ytdlp, base_opts
from lib.cookies import valid_cookie_file


def attempt(yt_dlp, url: str, cookie_file):
    opts = base_opts(cookies_file=valid_cookie_file(cookie_file))
    opts["extract_flat"] = "in_playlist"
    opts["skip_download"] = True
    opts["noplaylist"] = False  # override the single-video default
    with yt_dlp.YoutubeDL(opts) as ydl:
        return ydl.extract_info(url, download=False)


def normalize_entries(info: dict) -> list[dict]:
    out = []
    for e in info.get("entries") or []:
        if not e:
            continue
        url = e.get("url") or e.get("webpage_url")
        if not url:
            continue
        thumb = e.get("thumbnail")
        if not thumb and e.get("thumbnails"):
            thumb = e["thumbnails"][-1].get("url")
        out.append({
            "url": url,
            "title": e.get("title") or "Untitled",
            "duration": int(e["duration"]) if e.get("duration") else None,
            "thumbnail": thumb,
        })
    return out


def main() -> None:
    data = read_input()
    url = (data.get("url") or "").strip()
    if not url:
        emit_error("No URL provided.", "bad_input", retryable=False)

    yt_dlp = get_ytdlp()
    cookies = [c for c in (data.get("cookies_files") or []) if valid_cookie_file(c)]
    random.shuffle(cookies)
    candidates = [None] + cookies
    last = ("download_error", "Could not read this playlist.", True)

    for cookie in candidates:
        label = "public" if cookie is None else os.path.basename(cookie)
        try:
            info = attempt(yt_dlp, url, cookie)
        except yt_dlp.utils.DownloadError as e:
            last = classify(str(e))
            log(f"playlist [{label}] failed: {e}")
            if not cookies_might_help(last[0]):
                break
            continue
        except Exception as e:  # noqa: BLE001
            last = classify(str(e))
            log(f"playlist [{label}] error: {e}")
            if not cookies_might_help(last[0]):
                break
            continue

        entries = normalize_entries(info)
        if entries:
            emit_success({
                "title": info.get("title") or "Playlist",
                "platform": data.get("platform"),
                "count": len(entries),
                "entries": entries,
            })
        last = ("unsupported", "No playlist items found. Is this a playlist URL?", False)
        break

    emit_error(last[1], last[0], last[2])


if __name__ == "__main__":
    main()