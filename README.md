LARAVEL FILE UPLOAD 
=================================



TABLE OF CONTENTS
----------------
- Features
- Installation
- Configuration
- API Endpoints
- Postman Collection
- Testing
- Troubleshooting

FEATURES
--------

- Upload multiple files (up to 5 files, max 100MB each)
- Generate shareable download links
- Set expiration dates for uploads
- Password protection for downloads
- Email notifications
- Automatic cleanup of expired files
- File statistics

INSTALLATION
-----------

Prerequisites:
- PHP 8.1 or higher
- Composer
- SQLite support for PHP

Step 1: Clone the Repository
git clone https://github.com/Codinplus31/laravel-wetransfer.git



Step 2: Install Dependencies
composer install

Step 3: Set Up SQLite Database
# Create the database directory if it doesn't exist
mkdir -p database

# Create an empty SQLite database file
touch database/database.sqlite

# Set proper permissions
chmod 755 database
chmod 644 database/database.sqlite



# Generate application key
```php artisan key:generate```

Edit the .env file to use SQLite:
APP_NAME="File Upload API"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
 Comment out or remove these lines:
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=laravel
 DB_USERNAME=root
 DB_PASSWORD=

Step 5: Run Migrations
```php artisan migrate```

Step 6: Create Storage Symbolic Link

```php artisan storage:link```

Step 7: Start the Development Server
```php artisan serve```

Your API should now be running at http://localhost:8000.

CONFIGURATION
------------

Storage Configuration:
By default, uploaded files are stored in the storage/app/uploads directory. You can modify this in the config/filesystems.php file.

File Expiration:
Files are set to expire based on the expires_in parameter (default: 1 day). The cleanup command runs daily to remove expired files:



# Or run the cleanup command manually
```php artisan clean:expired-uploads```

API ENDPOINTS
------------

1. Upload Files

Endpoint: POST /api/upload

Description: Upload one or more files and get a shareable download link.

Request Parameters:
- files[] (required): Array of files to upload (max 5 files, 100MB each)
- expires_in (optional): Number of days until the files expire (default: 1)
- email_to_notify (optional): Email address to notify when files are uploaded
- password (optional): Password to protect the download

Response:
{
  "success": true,
  "download_link": "http://localhost:8000/api/download/a1b2c3d4e5f6g7h8i9j0"
}

2. Download Files

Endpoint: GET /api/download/{token}

Description: Download files using the provided token.

URL Parameters:
- token (required): The unique token received from the upload endpoint

Query Parameters:
- password (optional): Required if the upload is password-protected

Response: File download stream

3. View Upload Stats

Endpoint: GET /api/uploads/stats/{token}

Description: Get statistics about an upload.

URL Parameters:
- token (required): The unique token received from the upload endpoint

Response:
{
  "success": true,
  "data": {
    "token": "a1b2c3d4e5f6g7h8i9j0",
    "created_at": "2023-04-15 10:30:45",
    "expires_at": "2023-04-16 10:30:45",
    "expires_in_hours": 24,
    "download_count": 3,
    "file_count": 2,
    "total_size": 5242880,
    "total_size_formatted": "5 MB",
    "files": [
      {
        "name": "document.pdf",
        "size": "3 MB",
        "mime_type": "application/pdf"
      },
      {
        "name": "image.jpg",
        "size": "2 MB",
        "mime_type": "image/jpeg"
      }
    ]
  }
}

POSTMAN COLLECTION
-----------------
HERE IS THE DOCUMENTATION URL 

[https://documenter.getpostman.com/view/43924973/2sB2ixmEd7](https://documenter.getpostman.com/view/43924973/2sB2ixmEd7)


Import the Collection:
1. Download the Postman collection file call postmancollection.json from the repository
2. Open Postman
3. Click "Import" > "Upload Files" and select the downloaded file
4. The collection will be imported with all the endpoints configured

Setting Up Environment Variables:
Create a new environment in Postman with these variables:
- base_url: http://localhost:8000/api
- token: (This will be automatically set after your first upload)

Using the Collection:

Upload Files:
1. Select the "Upload Files" request
2. Go to the "Body" tab
3. Select "form-data"
4. Add the following key-value pairs:
   - Key: files[], Value: Select a file (repeat for multiple files)
   - Key: expires_in, Value: 1 (or any number of days)
   - Key: email_to_notify, Value: your-email@example.com (optional)
   - Key: password, Value: your-password (optional)
5. Click "Send"
6. The response will include a download link and the token will be automatically saved to your environment variables

Download Files:
1. Select the "Download Files" request
2. The token from your upload will be automatically used in the URL
3. If you set a password, add it as a query parameter:
   - Key: password, Value: your-password
4. Click "Send"
5. The file will be downloaded

View Upload Stats:
1. Select the "Upload Stats" request
2. The token from your upload will be automatically used in the URL
3. Click "Send"
4. You'll see statistics about your upload

TESTING
-------

Manual Testing:
1. Upload files using Postman or a web form
2. Try downloading the files using the provided link
3. Check the stats endpoint to verify file information
4. Test password protection by setting a password during upload
5. Test expiration by setting a short expiry time and running the cleanup command

Automated Testing:
Run the included test suite:
php artisan test

TROUBLESHOOTING
--------------

Common Issues:

"Could not find driver" Error:
This error occurs when the SQLite extension is not installed or enabled in PHP.

Solution:
# Install SQLite extension
sudo apt-get install php-sqlite3  # Ubuntu/Debian
brew install php                  # macOS

# Verify installation
php -m | grep sqlite

Database File Permission Issues:
If you encounter permission errors when accessing the database:

Solution:
# Set proper permissions
chmod 755 database
chmod 644 database/database.sqlite

File Upload Size Limitations:
If you can't upload large files, you may need to adjust PHP settings.

Solution:
Edit your php.ini file:
upload_max_filesize = 100M
post_max_size = 100M

Missing Storage Symbolic Link:
If uploaded files are not accessible:

Solution:
php artisan storage:link



API EXAMPLES
-----------

Example 1: Basic File Upload

Request:

``` POST /api/upload HTTP/1.1
Host: localhost:8000
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="files[]"; filename="document.pdf"
Content-Type: application/pdf

(Binary file content)
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="expires_in"

2
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

```
Response:
{
  "success": true,
  "download_link": "http://localhost:8000/api/download/a1b2c3d4e5f6g7h8i9j0"
}
```
Example 2: Password-Protected Upload

Request:
```
POST /api/upload HTTP/1.1
Host: localhost:8000
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="files[]"; filename="confidential.zip"
Content-Type: application/zip

(Binary file content)
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="password"

securepassword123
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

```
Response:
{
  "success": true,
  "download_link": "http://localhost:8000/api/download/k9l8m7n6o5p4q3r2s1t0"
}
```

Example 3: Download with Password

```
Request:
GET /api/download/k9l8m7n6o5p4q3r2s1t0?password=securepassword123 HTTP/1.1
Host: localhost:8000
```
Response: File download stream

Example 4: View Upload Stats

```
Request:
GET /api/uploads/stats/a1b2c3d4e5f6g7h8i9j0 HTTP/1.1
Host: localhost:8000
```

```
Response:
{
  "success": true,
  "data": {
    "token": "a1b2c3d4e5f6g7h8i9j0",
    "created_at": "2023-04-15 10:30:45",
    "expires_at": "2023-04-17 10:30:45",
    "expires_in_hours": 48,
    "download_count": 0,
    "file_count": 1,
    "total_size": 3145728,
    "total_size_formatted": "3 MB",
    "files": [
      {
        "name": "document.pdf",
        "size": "3 MB",
        "mime_type": "application/pdf"
      }
    ]
  }

}
```

