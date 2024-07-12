## Local Setup

### 1. Set Up Local Environment:

- Ensure XAMPP/WAMP/MAMP is installed and running.
- Start Apache and MySQL.

### 2. Database:

- Navigate to `http://localhost/phpmyadmin/`.
- Create a database named `classroom`.
- Copy all the code from `classroom.example.sql` file and run under the database `classroom` in `phpmyadmin`, which will create all the required tables.

### 3. Google OAuth Setup

1. **Go to Google Developer Console:**

   - Navigate to the [Google Developer Console](https://console.developers.google.com/).

2. **Create a New Project and Enable the OAuth Consent Screen:**

   - Click on the **"Select a project"** dropdown at the top.
   - Click **"New Project"**.
   - Enter a name for your project and click **"Create"**.
   - Once the project is created, go to the **OAuth consent screen** tab on the left sidebar.
   - Fill out the required fields and save the changes.

3. **Create OAuth Credentials:**

   - Go to the **Credentials** tab on the left sidebar.
   - Click on **"Create Credentials"** and select **"OAuth 2.0 Client IDs"**.
   - Select **Web application** as the application type.
   - Enter a name for your OAuth client.
   - Set **Authorized JavaScript origins** to your app's domain.
   - Set **Authorized redirect URIs** to your app's OAuth callback URL.
   - Click **"Create"**.

4. **Get CLIENT_ID and CLIENT_SECRET:**

   - After creating the OAuth client, you will see the **CLIENT_ID** and **CLIENT_SECRET**.
   - Copy and store these credentials securely.

5. **Set REDIRECT_URI:**

   - Set **REDIRECT_URI** to your app's OAuth callback URL.

### 4. Environment Variables:

- create a `.env` file in the root directory and add all the environmental variable as shown in the `.example.env` file.

```sh
APP_URL=http://localhost/classroom/
DB_HOST=localhost
DB_NAME=classroom
DB_USER=root
DB_PASS=''
CLIENT_ID=''
CLIENT_SECRET=''
REDIRECT_URI=''
```

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-index.png?raw=true)

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-home.png?raw=true)

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-class.png?raw=true)

![alt text](https://github.com/niladri-17/Virtual-Classroom/blob/main/readme-images/vc-submit.png?raw=true)
