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
    <h3 style="color: #146c94;">Reservation Confirmed</h3>
    
    <p>Hello our beloved guest,</p>
    <p>Your reservation for the resort {{$resort_name}} is confirmed. See below the full details.
    </p>
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
      <li>Resort Name: {{$resort_name}}</li>
      <li>Address: {{$resort_address}}</li>
      <li>Owner: {{$user_name}}</li>
      <li>Email: {{$user_email}}</li>
      <li>Contact No.: {{$user_contact}}</li>
    </ul>
    </br>
    <p><strong>Note:</strong> This will also serve you as a official reciept. Please inform the front desk.</p>
    <p>We look forward to see you soon,</br>
    QuickRent Online
    </p>

    </div>
    
    <p>© 2023 QUICKRENT ONLINE</br>
	
	</body>
</html>