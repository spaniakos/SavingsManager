#!/bin/bash
# Quick script to check latest Laravel errors
tail -100 storage/logs/laravel.log | grep -A 20 -B 5 "ERROR\|Exception\|SQLSTATE" | tail -50

