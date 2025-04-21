// // firebaseMessaging.ts
// import { getAnalytics } from "firebase/analytics";
// import { initializeApp } from "firebase/app";
// import { getMessaging, getToken } from "firebase/messaging";
// import { firebaseConfig } from "./firebaseConfig";

// // Initialize Firebase app and services
// const app = initializeApp(firebaseConfig);
// getAnalytics(app);
// const messaging = getMessaging(app);

// /**
//  * Registers a service worker, requests notification permission,
//  * and retrieves the Firebase Cloud Messaging token.
//  *
//  * @returns A Promise that resolves to the FCM token string or null if unavailable.
//  */
// export async function getFcmToken(): Promise<string | ''> {
//   if (typeof navigator === "undefined" || !('serviceWorker' in navigator)) {
//     throw new Error("Service workers are not supported in this environment.");
//   }
  
//   try {
//     // Register the service worker
//     const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
//     console.log("Service Worker registered with scope:", registration.scope);

//     // Request notification permission from the user
//     const permission = await Notification.requestPermission();
//     if (permission !== "granted") {
//       throw new Error("Notification permission denied");
//     }

//     // Retrieve the FCM token using the registered service worker
//     const token = await getToken(messaging, {
//       vapidKey: "BLR9jnCnnTjK3yX7kg69_ipFGY-lU-kASWjzzESILBc9AzDwbisE7XCTfA_nS1y6oTtA7WeXSrVUD1L5hueaBWs",
//       serviceWorkerRegistration: registration
//     });

//     if (token) {
//       console.log("FCM Token:", token);
//       return token;
//     } else {
//       console.log("No registration token available.");
//       return '';
//     }
//   } catch (error) {
//     console.error("Error retrieving token:", error);
//     throw error;
//   }
// }

