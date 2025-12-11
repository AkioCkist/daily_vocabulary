#!/usr/bin/env python3
import subprocess
import sys

try:
    # Create the test database
    result = subprocess.run(
        [r'C:\Program Files\PostgreSQL\18\bin\psql.exe', '-U', 'postgres', '-c', 'CREATE DATABASE daily_vocabulary_test;'],
        capture_output=True,
        text=True
    )
    
    if result.returncode == 0 or 'already exists' in result.stderr:
        print("Test database ready (created or already exists)")
    else:
        print(f"Error creating database: {result.stderr}")
        sys.exit(1)
        
except FileNotFoundError:
    print("psql not found. Make sure PostgreSQL is installed and in PATH")
    sys.exit(1)
