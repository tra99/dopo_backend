<script lang="ts" setup>
import { ref } from 'vue'
import type { Notification } from '@layouts/types'

const firebaseConfig = {
  apiKey: 'AIzaSyCWPEh8Pp62jr-fJboZ0tJm0JIXA_iLqrw',
  authDomain: 'dopo-aa2ab.firebaseapp.com',
  projectId: 'dopo-aa2ab',
  storageBucket: 'dopo-aa2ab.firebasestorage.app',
  messagingSenderId: '941958064824',
  appId: '1:941958064824:web:77cc16c49def7b046da24d',
  measurementId: 'G-17HS7TNYTP',
}

// const app = initializeApp(firebaseConfig)
// const messaging = getMessaging(app)

const notifications = ref<Notification[]>([
  // {
  //   id: 1,
  //   img: avatar4,
  //   title: 'Congratulation Flora! 🎉',
  //   subtitle: 'Won the monthly best seller badge',
  //   time: 'Today',
  //   isSeen: true,
  // },
  // {
  //   id: 2,
  //   text: 'Tom Holland',
  //   title: 'New user registered.',
  //   subtitle: '5 hours ago',
  //   time: 'Yesterday',
  //   isSeen: false,
  // },
  // {
  //   id: 3,
  //   img: avatar5,
  //   title: 'New message received 👋🏻',
  //   subtitle: 'You have 10 unread messages',
  //   time: '11 Aug',
  //   isSeen: true,
  // },
  // {
  //   id: 4,
  //   img: paypal,
  //   title: 'PayPal',
  //   subtitle: 'Received Payment',
  //   time: '25 May',
  //   isSeen: false,
  //   color: 'error',
  // },
  // {
  //   id: 5,
  //   img: avatar3,
  //   title: 'Received Order 📦',
  //   subtitle: 'New order received from john',
  //   time: '19 Mar',
  //   isSeen: true,
  // },
])

const removeNotification = (notificationId: number) => {
  notifications.value = notifications.value.filter(item => item.id !== notificationId)
}

const markRead = (notificationIds: number[]) => {
  notifications.value.forEach(item => {
    if (notificationIds.includes(item.id))
      item.isSeen = true
  })
}

const markUnRead = (notificationIds: number[]) => {
  notifications.value.forEach(item => {
    if (notificationIds.includes(item.id))
      item.isSeen = false
  })
}

const handleNotificationClick = (notification: Notification) => {
  if (!notification.isSeen)
    markRead([notification.id])
}

const notificationBody = ref('')

// Listen for incoming messages once the component is mounted
</script>

<template>
  <Notifications
    :notifications="notifications"
    @remove="removeNotification"
    @read="markRead"
    @unread="markUnRead"
    @click:notification="handleNotificationClick"
  />
  <!-- Optionally display the latest notification body -->
</template>
