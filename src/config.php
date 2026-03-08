<?php
/**
 * config.php – Security and runtime configuration.
 * Docker is now mandatory for safe execution.
 */

// Docker settings – must be properly configured
define('DOCKER_IMAGE', getenv('DOCKER_IMAGE') ?: 'python:3.11-slim');
define('DOCKER_MEMORY', getenv('DOCKER_MEMORY') ?: '512m');
define('DOCKER_CPUS', getenv('DOCKER_CPUS') ?: '1.0');
define('DOCKER_USER', getenv('DOCKER_USER') ?: '1000:1000'); // non‑root user ID

// Simulation limits
define('MAX_EXEC_TIME', 300);

// Allowed script names (whitelist)
define('ALLOWED_SCRIPTS', ['s5_core.py', 's5_runner.py', 's5_analysis.py', 'qnvm_light.py']);

// Directory permissions
define('DIR_PERMISSIONS', 0700);

// CSRF secret (must be set in environment)
define('CSRF_SECRET', getenv('CSRF_SECRET') ?: die('CSRF_SECRET not set'));

// API key (must be set in environment)
define('API_KEY', getenv('SOVEREIGN_API_KEY') ?: die('SOVEREIGN_API_KEY not set'));
