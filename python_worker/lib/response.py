"""I/O contract between Laravel and the Python workers: JSON in on stdin, one JSON object out on stdout."""
from __future__ import annotations

import json
import sys


def read_input() -> dict:
    raw = sys.stdin.read()
    if not raw.strip():
        return {}
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        emit_error("Invalid input payload.", "bad_input", retryable=False)
    return {}  # unreachable


def log(message: str) -> None:
    sys.stderr.write(f"[worker] {message}\n")
    sys.stderr.flush()


def emit_success(data: dict) -> None:
    payload = dict(data)
    payload["success"] = True
    sys.stdout.write(json.dumps(payload))
    sys.stdout.flush()
    sys.exit(0)


def emit_error(message: str, error_type: str = "unknown", retryable: bool = True) -> None:
    sys.stdout.write(json.dumps({
        "success": False,
        "error": str(message)[:500],
        "error_type": error_type,
        "retryable": retryable,
    }))
    sys.stdout.flush()
    sys.exit(1)