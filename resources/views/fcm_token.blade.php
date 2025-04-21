<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FCM Token Generator</title>
</head>
<body>
  <h1>Firebase Cloud Messaging Token Generator</h1>
  <p id="token-display"></p>

  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-app.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-analytics.js";
    import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-messaging.js";

    // Your Firebase configuration
    const firebaseConfig = {
      apiKey: "AIzaSyCWPEh8Pp62jr-fJboZ0tJm0JIXA_iLqrw",
      authDomain: "dopo-aa2ab.firebaseapp.com",
      projectId: "dopo-aa2ab",
      storageBucket: "dopo-aa2ab.firebasestorage.app",
      messagingSenderId: "941958064824",
      appId: "1:941958064824:web:77cc16c49def7b046da24d",
      measurementId: "G-17HS7TNYTP"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);
    const messaging = getMessaging(app);

    // Check if service workers are supported
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
          console.log("Service Worker registered with scope:", registration.scope);
          return Notification.requestPermission().then(permission => {
            if (permission === "granted") {
              // Retrieve the token using the registered service worker
              return getToken(messaging, {
                vapidKey: "BLR9jnCnnTjK3yX7kg69_ipFGY-lU-kASWjzzESILBc9AzDwbisE7XCTfA_nS1y6oTtA7WeXSrVUD1L5hueaBWs",
                serviceWorkerRegistration: registration
              });
            } else {
              throw new Error("Notification permission denied");
            }
          });
        })
        .then((currentToken) => {
          if (currentToken) {
            console.log("FCM Token:", currentToken);
            document.getElementById("token-display").innerText = "FCM Token: " + currentToken;
            // Optionally, send the token to your Laravel backend via AJAX
          } else {
            console.log("No registration token available.");
          }
        })
        .catch((err) => {
          console.error("Error retrieving token:", err);
        });
    } else {
      console.error("Service workers are not supported in this browser.");
    }
  </script>
</body>
</html>
