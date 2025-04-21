// firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js')
importScripts('https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging-compat.js')

firebase.initializeApp({
  apiKey: "AIzaSyCWPEh8Pp62jr-fJboZ0tJm0JIXA_iLqrw",
  authDomain: "dopo-aa2ab.firebaseapp.com",
  projectId: "dopo-aa2ab",
  storageBucket: "dopo-aa2ab.firebasestorage.app",
  messagingSenderId: "941958064824",
  appId: "1:941958064824:web:77cc16c49def7b046da24d",
  measurementId: "G-17HS7TNYTP"
})

const messaging = firebase.messaging()

messaging.onBackgroundMessage((payload) => {
  console.log('Received background message in service worker:', payload)
  const notificationTitle = payload.notification?.title || 'Background Notification'
  const notificationOptions = {
    body: payload.notification?.body,
    icon: '/firebase-logo.png' // Replace with your icon if needed
  }

  // Show the notification to the user.
  self.registration.showNotification(notificationTitle, notificationOptions)

  // Optionally, send a message to your app's client pages.
  self.clients.matchAll().then(clients => {
    clients.forEach(client => {
      client.postMessage({
        type: 'background-notification',
        payload: payload
      })
    })
  })
})
