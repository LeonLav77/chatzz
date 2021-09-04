<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->



<!DOCTYPE html>
<html class=''>
<link href="{{ url('/css/newChat.css') }}" rel="stylesheet">
<script src="/js/app.js"></script>
<script src="/js/bPopup.js"></script>

<head>
    <meta charset='UTF-8'>
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script src="https://use.typekit.net/hoy3lrg.js"></script>
    <script>
        try{Typekit.load({ async: true });}catch(e){}
    </script>
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
    <link rel='stylesheet prefetch'
        href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
    <script>
        $(document).ready(function() {
            window.currentActiveChat = 'global';
            var channel = Echo.channel('newChat'+'{{$isAuth->id }}');
            channel.listen("ReceiveNewChats", function(e) {
                console.log(e);
                addContactLocal(e.image,e.name,e.key,nothing);
                connectToChatSecondary(e.key,'SendPrivateMessage');

            });
            getMyChats({{ $isAuth->id }},loopThroughtChats)
            connectToChatMain('chat','SendMessage');

            window.addEventListener('popstate', function (event) {
                let queryString = window.location;
                let key = ((queryString.href).split('#'))[1];
                window.key = key;
                connectChats(key);
            });



            $.when(getMessages()).then(scrollCustom(0));

            document.getElementById('more').onclick = function() {
                    $.when(getMoreMessages()).then(scrollCustom(-400));
                };
            $(".messages").animate({ scrollTop: $(document).height() }, "fast");

            $("#profile-img").click(function() {
            $("#status-options").toggleClass("active");
            });

            $(".expand-button").click(function() {
            $("#profile").toggleClass("expanded");
            $("#contacts").toggleClass("expanded");
            });

            $("#status-options ul li").click(function() {
            $("#profile-img").removeClass();
            $("#status-online").removeClass("active");
            $("#status-away").removeClass("active");
            $("#status-busy").removeClass("active");
            $("#status-offline").removeClass("active");
            $(this).addClass("active");

            if($("#status-online").hasClass("active")) {
            $("#profile-img").addClass("online");
            } else if ($("#status-away").hasClass("active")) {
            $("#profile-img").addClass("away");
            } else if ($("#status-busy").hasClass("active")) {
            $("#profile-img").addClass("busy");
            } else if ($("#status-offline").hasClass("active")) {
            $("#profile-img").addClass("offline");
            } else {
            $("#profile-img").removeClass();
            };

            $("#status-options").removeClass("active");
        });
        startingBinds(sendMessageToServer);


    });
function startingBinds(callback){
{
    $('.submit').click(function() {
        callback();
    });

$(window).on('keydown', function(e) {
    if (e.which == 13) {
        callback();
        }
    });
}}

function newBinds(){
    let mess = $("#textInput").val();
    document.getElementById('textInput').value = ''
    id = {{ $isAuth->id }};
    sendPrivateMessage(mess,id);
}

function connectChats(key){
    window.whereToStart = 0;
    $(window).unbind('keydown');
    $(".submit").unbind();
    $("#more").unbind();
    resetChat();
    if(key != '') {
        document.getElementById('more').onclick = function() {
                    $.when(getMoreMessagesWithKey()).then(scrollCustom(-400));
        };
        $.when(getMoreMessagesWithKey()).then(scrollCustom(0));
        Echo.leave(window.currentChannel);
        var event = 'SendPrivateMessage';
        if(window.currentChannel == 'chat') {
            event = 'SendMessage';
        }
        connectToChatMain(key,'SendPrivateMessage');
        connectToChatSecondary(window.lastChannel,event);

        startingBinds(newBinds);

    }else{
        document.getElementById('more').onclick = function() {
            $.when(getMoreMessages()).then(scrollCustom(-400));
        };
        //alert("sad leavamo" + window.currentChannel)
        Echo.leave(window.currentChannel);
        startingBinds(sendMessageToServer);
        window.currentActiveChat = 'global';
        connectToChatMain('chat','SendMessage')
        $.when(getMessages()).then(scrollCustom(0));
        connectToChatSecondary(window.lastChannel,'SendPrivateMessage');
    }
}
    function getMoreMessagesWithKey() {
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/getMessageWithKey',
            type: 'get',
            data: {
                'whereToStart' : window.whereToStart,
                'key': window.key
            },
            success: function(response) {
                window.whereToStart = window.whereToStart + 15;
                    for (let i = 0; i < response.length; i++) {
                        let message = response[i];
                        let control = 'sent'
                            if(response[i].user_id == {{ $isAuth->id }}){ // ako je moja poruka
                                control = 'replies'
                            }
                        oldMessage(control,message.content,message,message.image);
                    }


                }
                });
            }

    function newMessage(who, text, sender,image) {
        if($.trim(text) == '') {
            return false;
        }
        $('<li class="'+who+' message"><img src="'+ image +'" alt="" /><p>' + text + '</p></li>').appendTo($('.messages ul'));
        $('.contact.active .preview').html('<span>'+sender+': </span>' + text);

    };

    function getMessages(){
        window.whereToStart = 0;
        $.ajax({
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/getMessage',
                type: 'get',
                data:{
                    'whereToStart':0
                },
                success: function(response) {
                    for (let i = 0; i < response.length; i++) {
                        let message = response[i];
                        let control = 'sent'
                            if(response[i].user_id == {{ $isAuth->id }}){ // ako je moja poruka
                                control = 'replies'
                            }
                        newMessage(control,message.content,message.user.name,message.user.image);

                    }
                }
                });
        }
    function scrollCustom(offset) {
        $(".messages").animate({ scrollTop: $(document).height() + offset }, "fast");
    }
    function getMoreMessages(){
        window.whereToStart = window.whereToStart + 15;
        $.ajax({
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/getMessage',
                type: 'get',
                data:{
                    'whereToStart':window.whereToStart
                },
                success: function(response) {
                    response.reverse();
                    for (let i = 0; i < response.length; i++) {
                        let message = response[i];
                        let control = 'sent'
                        if(response[i].user_id == {{ $isAuth->id }}){ // ako je moja poruka
                            control = 'replies'
                        }
                        oldMessage(control,message.content,message.user.name,message.user.image);
                    }
                }
                });
        }

        function resetChat() {
                $("#mainUL").empty();
        }
    function oldMessage(who, text, sender,image) {

        $('<li class="'+who+'"><img src="'+ image +'" alt="" /><p>' + text + '</p></li>').prependTo($('.messages ul'));

    };
        function sendMessageToServer(message) {
                let mess = $("#textInput").val();
                document.getElementById('textInput').value = ''
                    id = {{ $isAuth->id }};
                sendPublicMessage(mess,id);
                }

        function sendPrivateMessage(message,id) {
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/privateMessage",
                data: {
                    'message': message,
                    'id':id,
                    'key':window.key
                }
            });

        }
        function sendPublicMessage(message) {
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/message",
                data: {
                    'message': message,
                }
            });
        }
    function connectToChatMain(WhichChannel,WhichEvent){
        //alert("connecting TO MAIN " + WhichChannel + " " + WhichEvent);
        window.lastChannel = window.currentChannel;
        window.currentChannel = WhichChannel;
        var channel = Echo.channel(WhichChannel);
        channel.listen(WhichEvent, function(e) {
            let cont = "replies";
            if (e.id != "{{ $isAuth->id }}") {
                cont = "sent";
            }
            $.when(newMessage(cont, e.message, e.username,e.image)).then(scrollCustom(0));
        });
}

    function connectToChatSecondary(WhichChannel,WhichEvent){
        //alert(WhichChannel+" "+WhichEvent+" CONNECTING SECONDARY");
        var channel = Echo.channel(WhichChannel);
        channel.listen(WhichEvent, function(e) {
            //alert(e.key + " OVO JE KEY SEKUNDARNE PORUKE     " + e);
            console.log(e.key + " OVO JE KEY SEKUNDARNE PORUKE     ");
            console.log(e);
            $("#"+e.key).find(".preview").html("<span>"+e.username+": </span>"+e.message);
            console.log(e);
        });
}

function changeActiveChat(slika,ime,key) {
    $("#"+window.currentActiveChat).removeClass( 'active');
    $("#"+key).addClass( 'active');
    window.currentActiveChat = key;
    $('#profileImage').attr('src', slika);
    $('#profileName').html(ime);
}


function addChatsPopup(){
    $('#POPUP').bPopup({
            fadeSpeed: 'slow', //can be a string ('slow'/'fast') or int
            followSpeed: 1500, //can be a string ('slow'/'fast') or int
            modalColor: 'gray'
            });
}
function passiveChangeActiveChannel(slika,ime,key){
    changeActiveChat(slika,ime,key);
    document.location.href = "/#"+key;
}
function checkIfChatExists(myId,hisId) {
    if (myId == hisId) {alert("CANT MESSAGETO YOURSELF");return false;}
    else{
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/checkIfChatExists',
            type: 'post',
            data: {
                'myId':myId,
                'hisId':hisId
            },
            success: function(response) {
                console.log(response);
                console.log(response.statusCode);
                if (response.statusCode == 'added'){
                    var key = response.key;
                    var image = response.image;
                    var name = response.name;
                    addContactLocal(image,name,key,passiveChangeActiveChannel);
                    }
                else{
                    var key = response.key;;
                }
                var bPopup = $('#POPUP').bPopup();
                bPopup.close();

                }
            });
    }
}

function getMyChats(myId,callback){
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/getMyChats',
            type: 'get',
            data: {
                'id':myId,
            },
            success: function(response) {
                console.log(response);
                window.chats = response;
                for (let i = 0; i < response.length; i++) {
                    let currentChat = response[i];
                    connectToChatSecondary(currentChat['key'],'SendPrivateMessage');
                }
                callback();
                }
            });
    }
function loopThroughtChats(){
    for (let i = 0; i < (window.chats).length; i++) {
        let currentChat = window.chats[i];
        getLastMessages(currentChat['key']);
    }
}
function getLastMessages(key){
        $.ajax({
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/lastMessage',
            type: 'get',
            data: {
                'key':key,
            },
            success: function(response) {
                if(response != 'No Messages Yet'){
                    var name = response.username;
                    if (response.username == '{{ $isAuth->name }}'){
                        name = "You";
                    }
                    $("#"+key).find(".preview").html("<span>"+ name +": </span>"+response.content);
                }else{
                    $("#"+key).find(".preview").html("<span>No messages yet </span>");
                }
                }
            });
}
function nothing(){
    console.log("nothing");
}
function addContactLocal(image,name,key,callback){
    let aOuter = document.createElement('a');
    let liOuter = document.createElement('li');
    let divOuter = document.createElement('div');
    let spanOuter = document.createElement('span');
    let imgOuter = document.createElement('img');
    let divInner = document.createElement('div');
    let pOuter = document.createElement('p');
    let pInner = document.createElement('p');
    aOuter.setAttribute("href", "/#"+key);
    aOuter.setAttribute("class", "a");
    aOuter.setAttribute("onClick", 'changeActiveChat("'+image+'","'+name+'","'+key+'")');
    liOuter.setAttribute("class", "contact");
    liOuter.setAttribute("id", key);
    divOuter.setAttribute("class", "wrap");
    spanOuter.setAttribute("class", "contact-status online");
    imgOuter.setAttribute("src", image);
    imgOuter.setAttribute("width", "22px");
    imgOuter.setAttribute("height", "22px");
    imgOuter.setAttribute("alt", "");
    divInner.setAttribute("class", "meta");
    pOuter.setAttribute("class", "name");
    pOuter.innerHTML = name;
    pInner.setAttribute("class", "preview");
    pInner.innerHTML = 'No messages yet';
    aOuter.appendChild(liOuter);
    liOuter.appendChild(divOuter);
    divOuter.appendChild(spanOuter);
    divOuter.appendChild(imgOuter);
    divOuter.appendChild(divInner);
    divInner.appendChild(pOuter);
    divInner.appendChild(pInner);
    $('#contactsList').prepend(aOuter);
    callback(image,name,key);
}
    </script>
</head>

<body>

    <div id="frame">
        <div id="sidepanel">
            <div id="profile">
                <div class="wrap">
                    <img id="profile-img" src="{{$isAuth->image}}" class="online" alt="{{ $isAuth->name }}" />
                    <p>{{ $isAuth->name }}</p>
                    <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
                    <div id="status-options">
                        <ul>
                            <li id="status-online" class="active"><span class="status-circle"></span>
                                <p>Online</p>
                            </li>
                            <li id="status-away"><span class="status-circle"></span>
                                <p>Away</p>
                            </li>
                            <li id="status-busy"><span class="status-circle"></span>
                                <p>Busy</p>
                            </li>
                            <li id="status-offline"><span class="status-circle"></span>
                                <p>Offline</p>
                            </li>
                        </ul>
                    </div>
                    <div id="expanded">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </div>
            </div>
            <div id="search">
                <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
                <input type="text" placeholder="Search contacts..." />
            </div>


            <div id="contacts">
                <ul id="contactsList">
                    <a href="#" onclick='changeActiveChat("/storage/user_avatar/global.png","Global Chat","global")'>
                        <li class="contact active" id="global">
                            <div class="wrap">
                                <span class="contact-status online"></span>
                                <img src="/storage/user_avatar/global.png" alt="" />
                                <div class="meta">
                                    <p class="name">GLOBAL CHAT</p>
                                    <p class="preview"></p>
                                </div>
                            </div>
                        </li>
                    </a>
                    @foreach($myChats as $contact)
                    {{-- <h1>{{$contact}}</h1> --}}
                    {{-- <h1>{{$contact->secondPerson->id}}</h1>
                    <h1>{{$isAuth->id}}</h1> --}}
                    @if ($contact->secondPerson->id == $isAuth->id )
                    <a href="#{{ $contact->key }}" class="a" onclick='changeActiveChat("{{ $contact->firstPerson->image }}","{{ $contact->firstPerson->name }}","{{ $contact->key }}")'>
                        <li class="contact" id="{{ $contact->key }}">
                            <div class="wrap">
                                <span class="contact-status online"></span>
                                <img src="{{ $contact->firstPerson->image }}" alt="" />
                                <div class="meta">
                                    <p class="name">{{ $contact->firstPerson->name }}</p>
                                    <p class="preview">{{ $contact->user1 }}</p>
                                </div>
                            </div>
                        </li>
                    </a>
                    @else
                    <a href="#{{ $contact->key }}" class="a" onclick='changeActiveChat("{{ $contact->secondPerson->image }}","{{ $contact->secondPerson->name }}","{{ $contact->key }}")'>
                    <li class="contact" id="{{ $contact->key }}">
                        <div class="wrap">
                            <span class="contact-status online"></span>
                            <img src="{{ $contact->secondPerson->image }}" alt="" />
                            <div class="meta">
                                <p class="name">{{ $contact->secondPerson->name }}</p>
                                <p class="preview">{{ $contact->user2 }}</p>
                            </div>
                        </div>
                    </li>
                </a>
                    @endif
                    @endforeach
                </ul>
            </div>


            {{-- OVDJE JE CONTACT --}}
            <div id="bottom-bar">
                <button id="addcontact" onClick="addChatsPopup()"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add
                        contact</span></button>
                <button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
            </div>
        </div>


        {{-- TU IDU STVARI VEZANE ZA LIKA ONO GORE --}}
        <div class="content">
            <div class="contact-profile">
                <img src="/storage/user_avatar/global.png" alt="" id="profileImage" />
                <p id="profileName">Global Chat</p>
                <div class="social-media">
                    <i class="fa fa-facebook" aria-hidden="true"></i>
                    <i class="fa fa-twitter" aria-hidden="true"></i>
                    <i class="fa fa-instagram" aria-hidden="true"></i>
                </div>
            </div>


            {{-- TU IDU MESSAGOVI --}}
            <div class="messages">
                <p class="center" id="more">LOAD MORE MESSAGES</p>
                <ul id="mainUL">

                </ul>
            </div>

            {{-- OVDJE JE INPUT FIELD --}}
            <div class="message-input">
                <div class="wrap">
                    <input id="textInput" type="text" placeholder="Write your message..." />
                    <i class="fa fa-paperclip attachment" aria-hidden="true"></i>
                    <button class="submit" id="send"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>


    <div id="POPUP" style="display: none;" class="basic-grid">
        @foreach($people as $person)
        <div class="card">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $person->name }}</h3>
                </div>
                <div class="panel-body">
                    @if ($isAuth != '')
                    <img class="img-circle" src="{{ $person->image }}" alt="{{ $person->name }}" width="175" height="175">

                    <p><button type="button" class="btn" onclick="checkIfChatExists({{ $isAuth->id }},{{ $person->id }},'changeChannel')">New chat</button></p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
</div>


</body>

</html>
