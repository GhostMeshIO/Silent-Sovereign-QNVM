<?php
/**
 * config.php – Security and runtime configuration for Silent Sovereign QNVM.
 * Do not commit this file with real secrets to version control.
 */

// Authentication – set a strong, random API key in your environment
define('API_KEY', getenv('SOVEREIGN_API_KEY') ?: 'change-this-in-production');

// CSRF – secret used to generate tokens
define('CSRF_SECRET', getenv('CSRF_SECRET') ?: 'change-this-too');

// Whether to run Python simulations inside a Docker container (requires Docker)
define('USE_DOCKER', filter_var(getenv('USE_DOCKER') ?: 'false', FILTER_VALIDATE_BOOLEAN));

// Docker image to use for simulations (if USE_DOCKER is true)
define('DOCKER_IMAGE', getenv('DOCKER_IMAGE') ?: 'python:3.11-slim');

// Resource limits for the simulation process (seconds, memory)
define('MAX_EXEC_TIME', 300);
define('MAX_MEMORY', '512M'); // only used when not in Docker (ulimit)

// Allowed script names (whitelist)
define('ALLOWED_SCRIPTS', ['s5_core.py', 's5_runner.py', 's5_analysis.py', 'qnvm_light.py']);

// Output directory permissions
define('DIR_PERMISSIONS', 0700);
