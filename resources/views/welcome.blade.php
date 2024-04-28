<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusher Alerts</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
</head>
<body>
    <div id="app">
        <!-- Display alerts here -->
    </div>

    <script>
        var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: '{{env('PUSHER_APP_CLUSTER')}}'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            var messageDiv = document.createElement('p');
            messageDiv.textContent = data.message;
            document.getElementById('app').appendChild(messageDiv);
        });
    </script>
</body>
</html>