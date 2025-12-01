#!/bin/bash

# Test MailerLite webhook endpoint
echo "Testing webhook endpoint..."

curl -X POST https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "type": "subscriber.created",
    "data": {
      "email": "test@example.com",
      "name": "Test",
      "fields": {
        "submission_ref": "REF-TEST1234",
        "company": "Test Company",
        "message": "Test message"
      }
    }
  }' \
  -v
