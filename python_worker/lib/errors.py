"""Classify raw yt-dlp / network error text into a stable (error_type, message, retryable) tuple."""
from __future__ import annotations


def classify(text: str | None) -> tuple[str, str, bool]:
    t = (text or "").lower()

    if any(k in t for k in ["private", "login required", "log in", "this account is private",
                            "requested content is not available", "sign in to confirm you"]):
        return ("private_content", "This content is private or requires login.", False)

    if any(k in t for k in ["video unavailable", "not found", "no longer available",
                            "has been removed", "does not exist", "404"]):
        return ("not_found", "This content was not found or has been removed.", False)

    if any(k in t for k in ["429", "too many requests", "rate-limit", "rate limit"]):
        return ("rate_limited", "The site is rate-limiting requests. Please try again shortly.", True)

    if any(k in t for k in ["geo", "not available in your country", "blocked in your"]):
        return ("geo_blocked", "This content is geo-restricted.", False)

    if any(k in t for k in ["unsupported url", "no video formats", "unable to extract", "is not a valid url"]):
        return ("unsupported", "This URL or content type is not supported.", False)

    if any(k in t for k in ["timed out", "timeout", "connection", "network", "resolve host"]):
        return ("network_error", "A network error occurred. Please try again.", True)

    return ("download_error", "Could not process this content.", True)