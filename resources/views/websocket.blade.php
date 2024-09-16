<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    PUSHER TEST
    <script>
        // fetch("route('api.v2.auth.login', ['provider' => 'email'])}}", {
        //     method: "POST",
        //     headers: { "Content-Type": "application/json", "Accept": "application/json" },
        //     credentials: 'include',
        //     body: JSON.stringify({
        //         "email": "email@example.com",
        //         "password": "pwd12oe@rw34"
        //     })
        // })
        // .then(response => {
        //     response.json()
        //     console.log('Success:', response);
        // fetch("route('api.v2.user.me')}}", {
        //         headers: { "Content-Type": "application/json", "Accept": "application/json" },
        //         credentials: 'include'
        //     })
        //         .then(res => res.json().then(data => connectWS(data.data)))
        //         .catch(error => console.error('Error me:', error));
        // })
        // .catch(error => console.error('Error login:', error));

        fetch("{{route('api.v2.user.me')}}", {
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            credentials: 'include'
        })
            .then(res => res.json().then(data => connectWS(data.data)))
            .catch(error => console.error('Error me:', error));

        function connectWS(user) {
            console.log('User: ', user);

            const pusherConfig = {
                wsHost: '{{env('PUSHER_PUBLIC_HOST')}}',
                wssPort: 6002,
                forceTLS: true,
                encrypted: false,
                enabledTransports: ['ws', 'wss'],
                // authEndpoint: '/api/broadcasting/auth',
                // auth: {
                //     Accept: "application/json",
                // }
            };

            console.log('Pusher config: ', pusherConfig);

            const pusher = new Pusher(
                '{{env('PUSHER_APP_KEY')}}',
                pusherConfig);

            pusher.subscribe('test').bind('event.public', (data) => {
                console.log('Received event: ', data);
            });

            let notifyChannel = pusher.subscribe(`notification.${user.id}`, (data) => {
                console.log('Received notification: ', data);
            });
            notifyChannel.bind('transaction.success', (data) => {
                console.log('Received USDT transaction: ', data);
            });

            // notifyChannel.bind('', (data) => { console.log('Received notification: ', data); });
            // pusher.subscribe(`private-notification.${user.id}`, (data) => {
            //     console.log('Received USDT transaction: ', data);
            // });
        }
    </script>
</body>

</html>