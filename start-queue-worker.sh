#!/bin/bash

# Start Laravel Queue Worker for Real-Time Notifications
# This script runs the queue worker for processing notifications

echo "🚀 Starting Laravel Queue Worker for Real-Time Notifications..."
echo "📧 Processing queues: instant, high, default"
echo "⏹️  Press Ctrl+C to stop"
echo ""

# Run the queue worker with priority queues
php artisan queue:work --queue=instant,high,default --verbose --tries=3 --timeout=90 --sleep=3

echo ""
echo "✅ Queue worker stopped" 