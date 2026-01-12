#!/bin/bash

echo "================================"
echo "Batch Media Conversion Script"
echo "================================"
echo ""

# Switch to GD driver
echo "Step 1: Switching to GD driver..."
sed -i '/IMAGE_DRIVER/d' .env
echo "IMAGE_DRIVER=gd" >> .env
php artisan config:cache
echo "✓ Done"
echo ""

# Process in batches of 10 media items
echo "Step 2: Processing conversions in batches..."
echo "This will take a while. Progress will be shown."
echo ""

BATCH_SIZE=10
TOTAL=$(php artisan tinker --execute="echo \Spatie\MediaLibrary\MediaCollections\Models\Media::count();")
BATCHES=$((($TOTAL + $BATCH_SIZE - 1) / $BATCH_SIZE))

echo "Total media: $TOTAL"
echo "Batch size: $BATCH_SIZE"
echo "Total batches: $BATCHES"
echo ""

for ((i=0; i<$BATCHES; i++)); do
    START_ID=$(($i * $BATCH_SIZE + 1))
    END_ID=$((($i + 1) * $BATCH_SIZE))
    
    echo "Processing batch $((i+1))/$BATCHES (IDs $START_ID-$END_ID)..."
    
    # Process this batch
    for ((id=$START_ID; id<=$END_ID; id++)); do
        php artisan media-library:regenerate --ids=$id --force 2>/dev/null
    done
    
    echo "✓ Batch $((i+1)) complete"
    
    # Small delay to prevent memory issues
    sleep 1
done

echo ""
echo "================================"
echo "All batches complete!"
echo "================================"
