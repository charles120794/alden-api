<!-- Notification / Reciept sent to the user -->
<!doctype html>
<html lang="en">
	<head>
    <style>
    .card-background{
      background-color:white;
      width:50%;
      height: content-fit;
      padding: 3rem;
      border-radius: 1rem;
    }
    body{
      background-color:#F5F5F5;
      display:flex;
      flex-direction:column;
      align-items: center;
    }
    p{
      font-family:verdana;
    }
    </style>
	</head>
	<body>
    <div class="card-background" >
    <h3 style="color: salmon;">Reservation Rejected</h3>
    
    <p>Hello our beloved guest,</p>
    <p>This will inform your reservation at {{$resort_name}} has been rejected by the owner. 
      If there are misunderstanding you can contact the resort owner, 
      see the details of the resort owner below.</p>
    
    </br>

    <p><strong>Here are the reservation summary:</strong></p>
    <ul>
      <li>Reservation Date: {{$reserve_date}}</li>
      <li>Description: {{$price_desc}}</li>
      <li>Payment Reference No.: {{$ref_no}}</li>
    </ul>

    </br>

    <p><strong>The resort and owner's information:</strong></p>
    <ul>
      <li>Resort: {{$resort_name}}</li>
      <li>Address: {{$resort_address}}</li>
      <li>Owner: {{$user_name}}</li>
      <li>Email: {{$user_email}}</li>
      <li>Contact No.: {{$user_contact}}</li>
    </ul>
    </br>
    <p>The owner said: </br>
    {{$note}}
    </p>
    </br>
    <p>Once again, we apologize for any inconvenience caused and appreciate your understanding in this 
      matter. We value your interest in {{$resort_name}} and hope for the opportunity to
      welcome you or your guests in the future.</p>
    <p>Warm regards, </br>
    QuickRent Online
    </p>

    </div>
    
    <p>© 2023 QUICKRENT ONLINE</br>
	
	</body>
</html>