<?php
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
}

require("php/database.php");

$user_query = sendQuery("SELECT * FROM organizer WHERE username='".$username."'");
if($user_query) {
    $user = $user_query->fetch_assoc();
    $first_name = $user['first_name'];
} else {
    echo '<script type="text/javascript"> alert("Sorry, you do not have access to this page."); location="login.php"; </script>';
}

# For total number of events
$event_query = "SELECT * FROM events WHERE organizer='" . $username . "' ";

$events = sendQuery($event_query);
$events_array = array();

if ($events) {
    while($row = $events->fetch_assoc())  {
        $events_array[] = $row;
    }
}

# For upcoming events
$event_query1 = "SELECT * FROM events WHERE organizer='" . $username . "' && date > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY) ORDER BY date";

$events1 = sendQuery($event_query1);
$events_array1 = array();

if ($events1) {
    while($row = $events1->fetch_assoc())  {
        $events_array1[] = $row;
    }
}
# For total number of tickets sold
$event_query2 = "SELECT count(*) as count FROM events, event_ticket WHERE organizer='" . $username . "' && event_ticket.event_id = events.event_id";

$events2 = sendQuery($event_query2);
$events_array2 = array();

if ($events2) {

    while($row = $events2->fetch_assoc())  {
        $events_array2[] = $row;
    }
}
# For total number of users
$event_query3 = "select distinct(username) from itshappening.event_ticket as et, itshappening.event_registration as er, itshappening.events as e WHERE organizer='" . $username . "' && er.ticket_id = et.ticket_id && et.event_id = e.event_id";

$events3 = sendQuery($event_query3);
$events_array3 = array();

if ($events3) {
    while($row = $events3->fetch_assoc())  {
        $events_array3[] = $row;
    }
}
#For total revenue
$event_query4 = "SELECT sum(price) as total FROM itshappening.event_ticket where event_id in (select event_id from events WHERE organizer='" . $username . "')";

$events4 = sendQuery($event_query4);
$events_array4 = array();

if ($events4) {
    while($row = $events4->fetch_assoc())  {
        $events_array4[] = $row;
    }
}
# For total number of tickets sold per event
$event_query5 = "select * from event_ticket et, events e where et.event_id = e.event_id and e.event_id = '3' and organizer '" . $username . "')";

$events5 = sendQuery($event_query5);
$events_array5 = array();

if ($events5) {
    while($row = $events5->fetch_assoc())  {
        $events_array5[] = $row;
    }
}
# For total number of users for that event
$event_query6 = "select count(*) as cnt from event_ticket et, events e where et.event_id = e.event_id and e.event_id = '3' and organizer '" . $username . "')";

$events6 = sendQuery($event_query6);
$events_array6 = array();

if ($events6) {
    while($row = $events6->fetch_assoc())  {
        $events_array6[] = $row;
    }
}

# For revenue generated by event
$event_query7 = "select count(*) as cnt from event_ticket et, events e where et.event_id = e.event_id and e.event_id = '3' and organizer '" . $username . "')";

$events7 = sendQuery($event_query7);
$events_array7 = array();

if ($events7) {
    while($row = $events7->fetch_assoc())  {
        $events_array7[] = $row;
    }
}

#Bar Chart
$event_query8 = "SELECT monthname(create_date) AS Month, sum(event_amt) AS 'totalrevenue' FROM events where organizer = '" . $username . "' GROUP BY month(create_date)";

$events8 = sendQuery($event_query8);
$events_array8 = array();

if ($events8) {
    while($row = $events8->fetch_assoc())  {
        $events_array8[] = $row;
    }
}
//echo var_dump($events_array7);
//echo json_encode($events_array7);
?>



<html>
    <head>
        <title>It's Happening!</title>
        <link href="css/global-header.css" rel="stylesheet">
        <link href="css/organiserstyle.css" rel="stylesheet">
        <link href="css/barchart.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css" rel="stylesheet">
        <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="css/home.css" rel="stylesheet">
        <link href="css/font-awesome.css" rel="stylesheet">
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/dashboarddata.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>

        <style>
            canvas {
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
            }
        </style>

        <style>
            .eventLists
            {
                font-family: 'Roboto', sans-serif;
            }
        </style>

    </head>
    <body>
    <body>
        <!-- global header -->
        <ul class='global-header'>
            <?php
            if (isset($_SESSION['username'])) {
                echo "<li class='global-header-list'><a href='logout.php'>Sign Out</a></li>";
            } else {
                echo "<li class='global-header-list'><a href='login.php'>Sign In / Sign Up</a></li>";
            }
            ?>
            <li class='global-header-list'><a href="createEvent.php">Create Event</a></li>
            <li class='global-header-list'><a href="home.php">Home</a></li>
            <li class='global-header-list'><a href="home.php#aboutUs">About Us</a></li>
            <li class='global-header-list-left' style = "color: white; margin-top: 1.2%; margin-right: 15%;" align = center>
                <?php if (isset($username)) {echo "Welcome! ".ucfirst($first_name);}?>
            </li>
        </ul>

        <div style="height: 400px; width:100%; background: white; display: flex;">

            <div id="canvas-holder" style="height: 400px; width:400px;margin:0 15%;">
                <canvas id="chart-area"></canvas>

            </div>

            <dl>
                <dt align = "center">
                  Revenue Generated by month
                </dt>
                <?php
                //echo json_encode($events_array7);
                    $totalCost = 0;
                    foreach( $events_array8 as $event){
                        $totalCost = (int)$totalCost + (int)($event["totalrevenue"]);

                    }
                    //echo $totalCost;

                    foreach( $events_array8 as $event){
                    $percent = ceil((float)($event["totalrevenue"]/$totalCost) *100);
                    //echo $percent;
                    //$classper = "percentage-"+(string)$percent;
                    ?>
                <dd class="percentage percentage-<?php echo $percent?>"><span class="text"><?php echo $event["Month"]." : $".$event["totalrevenue"]*5;?></span></dd>
<!--//                <dd class="percentage percentage-20"><span class="text">September: $2500</span></dd>
//                <dd class="percentage percentage-16"><span class="text">October: $1070</span></dd>
//                <dd class="percentage percentage-5"><span class="text">November: $750</span></dd>
//                <dd class="percentage percentage-2"><span class="text">December: $550 </span></dd>
//                <dd class="percentage percentage-2"><span class="text">January: $550</span></dd>-->
                <?php
                    }
                   ?>
              </dl>

<!--            <div id="canvas-holder1" style="height: 400px; width:400px; margin-right: 15%;">
                <canvas id="chart-area1"></canvas>

            </div>
-->
        </div>
        <div class = "eventLists" style=" background: rgb(218,238,246); display: flex;">
            <div id = "myevents" class = "eventList" style="width: 50%; margin-left: 2.5%; height: auto; background: rgb(218,238,246);">
                <h4 align = center> My Events</h4>
                <ul class="ulist collection">
                    <?php
                    $totalEvents = 0;
                    foreach( $events_array as $event){
                        $totalEvents = $totalEvents +1;
                    ?>
<!--                <script>
                    var imagevar = <?php echo $event["image"]; ?>
                    </script>-->
                    <!--(<?php echo $event["image"]; ?>)-->
                    <!--
                    <div class="col s12 m6 l6 listitem modal-trigger event-item"
                    data-target="modal1"
                    data-eventid="<?php echo $event["event_id"]; ?>"
                    data-organizer="<?php echo $username; ?>"
                    data-eventname="<?php echo $event["name"]; ?>"
                    data-eventdesc="<?php echo $event["description"]; ?>"
                    data-eventimg="<?php echo $event["image"]; ?>"
                    >
                      <div class="card horizontal">
                        <div class="card-image">
                          <img src="<?php echo $event["image"]; ?>">
                        </div>
                        <div class="card-stacked">
                          <div class="card-content">
                            <h3><?php echo $event["name"]; ?></h3>
                            <p><?php echo $event["description"]; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                -->
                    <li class = "collection-item listitem modal-trigger event-item"
                    data-target="modal1"
                    data-eventid="<?php echo $event["event_id"]; ?>"
                    data-organizer="<?php echo $username; ?>"
                    data-eventname="<?php echo $event["name"]; ?>"
                    data-eventdesc="<?php echo $event["description"]; ?>"
                    data-eventimg="<?php echo $event["image"]; ?>"
                    style="border-radius: 5px; margin-bottom: 20px; -webkit-box-shadow: 1px 1px 4px 0px rgba(50, 50, 50, 0.75);
-moz-box-shadow:    1px 1px 4px 0px rgba(50, 50, 50, 0.75);
box-shadow:         1px 1px 4px 0px rgba(50, 50, 50, 0.75);">
                        <img class = "listimg" src=<?php echo $event["image"]; ?>>
                        <h3 class="eventTitle">
                            <?php echo $event["name"]; ?>
                        </h3>
                        <p class="eventdesc" >
                            <?php echo $event["description"]; ?>
                        </p>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <div class = "eventList" style="width: 50%; height: auto; background: rgb(218,238,246);">
                <h4 align = center> Upcoming Events</h4>
                <ul class="ulist collection">
                    <?php
                    $TotalUpcomingEvents = 0;
                    foreach( $events_array1 as $event1){
                        $$TotalUpcomingEvents = $TotalUpcomingEvents + 1;
                    ?>
                    <li class = "listitem collection-item" style="border-radius: 5px; margin-bottom: 20px; -webkit-box-shadow: 1px 1px 4px 0px rgba(50, 50, 50, 0.75);
-moz-box-shadow:    1px 1px 4px 0px rgba(50, 50, 50, 0.75);
box-shadow:         1px 1px 4px 0px rgba(50, 50, 50, 0.75);">
                        <img class = "listimg" src=<?php echo $event1["image"]; ?> >
                        <h3 class="eventTitle">
                            <?php echo $event1["name"]; ?>
                        </h3>
                        <p class="eventdesc" >
                            <?php echo $event1["description"]; ?>
                        </p>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>

        <input type="hidden" id="events_array" value='<?php echo $totalEvents;?>' />
        <input type="hidden" id="events_array1" value='<?php echo $TotalUpcomingEvents; ?>' />
        <input type="hidden" id="events_array2" value='<?php echo htmlentities( json_encode($events_array2)); ?>' />
        <input type="hidden" id="events_array3" value='<?php echo htmlentities( json_encode($events_array3)); ?>' />
        <input type="hidden" id="events_array4" value='<?php echo htmlentities( json_encode($events_array4)); ?>' />
<!--        <input type="hidden" id="events_array5" value='<?php echo htmlentities( json_encode($events_array5)); ?>' />
        <input type="hidden" id="events_array6" value='<?php echo htmlentities( json_encode($events_array6)); ?>' />
        <input type="hidden" id="events_array7" value='<?php echo htmlentities( json_encode($events_array7)); ?>' />-->

        <!-- Modal Structure -->
        <div id="modal1" class="modal">
            <div class="modal-content">
                <h4>Event Details</h4>
                <div class="card-stats">
                    <div class="row">
                        <div class="col s6">
                          <div class="card">
                            <div class="card-content teal accent-4 white-text">
                              <p class="card-stats-title">
                                <i class="material-icons">bookmark</i> Tickets sold</p>
                              <h4 class="card-stats-number"><span class="tickets-sold"></span></h4>
                            </div>
                          </div>
                        </div>

                        <div class="col s6">
                          <div class="card">
                            <div class="card-content deep-orange accent-2 white-text">
                              <p class="card-stats-title">
                                <i class="material-icons">attach_money</i>Revenue:</p>
                              <h4 class="card-stats-number"><span class="revenue"></span></h4>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="card small">
                      <div class="card-image">
                        <img src="" class="event-image" alt="sample">
                        <span class="card-title event-name" style="text-shadow: 2px 2px 2px #1C6EA4;"></span>
                      </div>
                      <div class="card-content event-description">
                        <p>I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively.</p>
                      </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
            </div>
        </div>
        <!-- LiveChat (www.livechatinc.com) -->
        <script type="text/javascript">
            window.__lc = window.__lc || {};
            window.__lc.license = 10377037;
            (function () {
                var lc = document.createElement('script');
                lc.type = 'text/javascript';
                lc.async = true;
                lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(lc, s);
            })();
        </script>

        <noscript>
        <a href="https://www.livechatinc.com/chat-with/10377037/">Chat with us</a>,
        powered by <a href="https://www.livechatinc.com/?welcome" rel="noopener" target="_blank">LiveChat</a>
        </noscript>
        <!-- LiveChat End -->
    </body>
    <script>
//        console.log( $("#events_array").data("events"));
//        console.log( $("#events_array1").data("events1"));
//        console.log( $("#events_array2").data("events2"));
//        console.log( $("#events_array3").data("events3"));
//        console.log( $("#events_array4").data("events4"));
//        console.log( $("#events_array5").data("events5"));
//        console.log( $("#events_array6").data("events6"));
//        console.log( $("#events_array7").data("events7"));

        $(document).ready(function(){
            $('.modal').modal();

            $(".event-item").click( function(){

                var eventId = $(this).data("eventid");
                var organizer = $(this).data("organizer");
                $(".event-name").html( $(this).data("eventname"));
                $(".event-image").attr("src", $(this).data("eventimg"));
                $(".event-description").html( $(this).data("eventdesc"));
                $.ajax({
                    method: "POST",
                    url: "Search.php",
                    data: {eventdetails: true, eventId: eventId, organizer: organizer}
                }).done(function (data) {
//                    console.log("Search data is --"+data)
                    var searchData = $.parseJSON(data);
//                    console.log(data[0])
                    console.log("Search data is "+JSON.stringify(searchData));
                    console.log(searchData.count);
                    console.log(searchData.revenue);
                    $(".tickets-sold").html( searchData.count);
                    $(".revenue").html(searchData.revenue);
                });
                //$("#modal1").
                //$(this).data("organizer");


            });
        });

    </script>
</html>
