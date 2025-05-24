import requests
import base64
from datetime import datetime

# Your credentials
consumer_key = "YOUR_CONSUMER_KEY"
consumer_secret = "YOUR_CONSUMER_SECRET"

# Step 1: Get access token
def get_access_token():
    consumer_key = "sFq4EsIoMma1uBxITeeJCHsF7d6MEGgyzdGRlWOJVWUkjovX"
    consumer_secret = "w7tC5dFL1T4C4rvh2djJtXGxt4TkPkXvEa5vu63aOSL5XBb7cL3Cxyos8iHfFP4m"

    auth_url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"
    response = requests.get(auth_url, auth=(consumer_key, consumer_secret))

    if response.status_code != 200:
        print("Error response:")
        print(response.text)
        response.raise_for_status()

    return response.json().get("access_token")

# Step 2: Send STK Push request
def initiate_stk_push():
    access_token = get_access_token()
    
    timestamp = datetime.now().strftime('%Y%m%d%H%M%S')
    short_code = "174379"
    passkey = ""
    # Generate base64 encoded password
    data_to_encode = short_code + passkey + timestamp
    password = base64.b64encode(data_to_encode.encode()).decode('utf-8')

    headers = {
        "Authorization": f"Bearer {access_token}",
        "Content-Type": "application/json"
    }

    payload =   {
    "BusinessShortCode": 174379,
    "Password": "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjUwNTI0MTYyMzQ4",
    "Timestamp": "20250524162348",
    "TransactionType": "CustomerPayBillOnline",
    "Amount": 1,
    "PartyA": 254741232714,
    "PartyB": 174379,
    "PhoneNumber": 254741232714,
    "CallBackURL": "https://mydomain.com/path",
    "AccountReference": "CompanyXLTD",
    "TransactionDesc": "Payment of X" 
  }

    response = requests.post(
        "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest",
        headers=headers,
        json=payload
    )
    print(response.json())

# Run it
initiate_stk_push()
