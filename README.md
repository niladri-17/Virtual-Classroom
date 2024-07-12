## Local Setup

### 1. Set Up Local Environment:
- Ensure XAMPP/WAMP/MAMP is installed and running.
- Start Apache and MySQL.

### 2. Database:
- Navigate to `http://localhost/phpmyadmin/`.
- Create a database named `classroom`.

### 3. Environment Variables:
```sh
APP_URL=http://localhost/classroom/
DB_HOST=localhost
DB_NAME=classroom
DB_USER=root
DB_PASS=""
CLIENT_ID=""
CLIENT_SECRET=''
REDIRECT_URI=''

4. Google OAuth Setup:
Go to Google Developer Console.
Create a new project and enable the OAuth consent screen.
Create OAuth credentials to get CLIENT_ID and CLIENT_SECRET.
Set REDIRECT_URI to your app's OAuth callback URL.

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-index.png?raw=true)

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-home.png?raw=true)

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-class.png?raw=true)

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-submit.png?raw=true)

