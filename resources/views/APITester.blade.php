<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Document</title>

    <script>
        function ajaxCall(message){
            if (window.id != "NOT LOGGED IN"){
            $.ajax({
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/message',
                data:{
                    'message':message,
                },
                type: 'post',
                success: function(response) {
                    console.log(response)
                }
            });
                }else{
                    alert("You are not logged in");
                }}
        function ajaxCallWithKey(message){
            alert(keys[0])
            if (window.id != "NOT LOGGED IN"){
                $.ajax({
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/privateMessage",
                    data: {
                            'message': message,
                            'id':window.id,
                            'key':keys[0]
                    },
                    type: 'post',
                        success: function(response) {
                        console.log(response)
                    }
                });
                }else{
            alert("You are not logged in");
        }}
        function checkIfLoggedIn(){
                $.ajax({
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/checkIfLoggedIn',
                    type: 'get',
                    success: function(response) {
                        console.log(response)
                        window.id = response;
                        console.log(window.id);
                        checkMyChats();
                    }
                    });
                }
        function ajaxLogin(email,password){
                $.ajax({
                    dataType: 'json',
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/login',
                    data:{
                        'email':email,
                        'password':password
                    },
                    type: 'post',
                    success: function(response) {
                        console.log(response)
                        window.location.replace("/APITester");
                }
                });
            }

            function ajaxLoginReact(email,password){
                $.ajax({
                    dataType: 'json',
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/loginReact',
                    data:{
                        'email':email,
                        'password':password
                    },
                    type: 'post',
                    success: function(response) {
                        console.log(response)
                }
                });
            }


            function getSessionData(){
                $.ajax({
                    dataType: 'json',
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/sessionData',
                    type: 'get',
                    success: function(response) {
                        console.log(response)
                }
                });
            }


            function ajaxRegister(email,password,name){
            const selectedFile = document.getElementById('picture').files[0];
            console.log(selectedFile);
            var formData = new FormData();
            formData.append('picture', selectedFile);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('password_confirmation', password);
            formData.append('name', name);
            $.ajax({
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/register',
                        processData: false,
                        contentType: false,
                        data: formData,
                        type: 'post',
                        success: function(response) {
                            console.log(response)
                        }
                    });
                }
        function logout() {
                $.ajax({
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/logout',
                    type: 'post',
                    success: function(response) {
                        console.log(response)
                    }
                    });
                }
        function checkMyChats() {
            $.ajax({
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/getMyChats',
                type: 'get',
                data: {
                    'id':window.id
                },
                success: function(response) {
                    console.log(response)
                    keys = [];
                    for (var i = 0; i < response.length; i++) {
                        var chat = response[i].key;
                        keys.push(chat);
                    }
                }
            });
        }
        function getMessagesWithKey() {
            $.ajax({
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/getMessageWithKey',
                type: 'get',
                data: {
                    'key': keys[0]
                },
                success: function(response) {
                    console.log(response)

                    }
                    });
                }
        $(document).ready(function() {
            checkIfLoggedIn();

         });
    </script>
</head>

<body>
    <div class="mt-4">
        <form method="POST" enctype="multipart/form-data" name="formName">
            <x-input id="picture" class="block mt-1 w-full" type="file" name="picture" :value="old('picture')"
                required />
        </form>
    </div>
    <button onclick="ajaxCall('waddup');">MESSAGE</button>
    <button onclick="checkIfLoggedIn();">isLoggedIn</button>
    <button onclick="ajaxLogin('leonlav77@gmail.com','password');">Login</button>
    <button onclick="ajaxRegister('leonlav7777777@gmail.com','password','masko');">Register</button>
    <button onclick="logout();">Logout</button>
    <button onclick="ajaxCallWithKey('hello private chat');">MESSAGE WITH KEY</button>
    <button onclick="checkMyChats();">GET MY CHATS</button>
    <button onclick="getMessagesWithKey();">GET MESSAGES WITH KEY</button>
    <br />
    <br />

    <button onclick="ajaxLoginReact('leonlav77@gmail.com','password');">Login With react</button>

    <button onclick="getSessionData();">getSesh</button>

</body>

</html>
