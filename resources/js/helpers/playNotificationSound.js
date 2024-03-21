export default async function playNotificationSound() {
    try {
        const audio = new Audio('/sounds/notification.mp3')
        await audio.play()
    } catch (e) {
        console.warn(e)
    }

}
