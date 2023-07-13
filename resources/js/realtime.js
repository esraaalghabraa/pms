import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'your_app_key',
    cluster: 'your_app_cluster',
    encrypted: true,
    // Add any other necessary configuration options
    // ...
    // For authentication
    auth: {
        headers: {
            Authorization: 'Bearer ' + yourAuthToken,
        },
    },
});
const channel = window.Echo.channel('channel-name');

channel.listen('EventName', (data) => {
    console.log(data);
    // Handle the received event data
});
