"""Validate a Netscape-format cookie file before handing it to yt-dlp."""
from __future__ import annotations

import os


def valid_cookie_file(path: str | None) -> str | None:
    if not path:
        return None
    if not os.path.isfile(path) or not os.access(path, os.R_OK):
        return None
    if os.path.getsize(path) < 20:
        return None
    return path