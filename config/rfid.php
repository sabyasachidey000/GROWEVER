<?php

return [
    // Rapid re-scans inside this window are retained in raw logs but ignored as business punches.
    'debounce_seconds' => (int) env('RFID_DEBOUNCE_SECONDS', 5),
];
