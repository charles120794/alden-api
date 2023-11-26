<!-- Notification / Reciept sent to the user -->
<!doctype html>
<html lang="en">
	<head>
	
	</head>
	<body>
    
    <div class="card-background" style="background: white; padding: 3rem 4rem;">
      <h3 style="color: #146c94;">Reservation</h3>
    
      <p>Hello our beloved owner,</p>
      <p>These are to inform you that a reservation has 
      been made for {{$resort_name}} during the specified dates. I would like to share the reservation
      details with you and kindly request your confirmation regarding the acceptance or declination of this reservation.
      </p>

      </br>

      <p><strong>Here are the reservation summary:</strong></p>
      <ul>
        <li>Reservation Date: {{$reserve_date}}</li>
        <li>Description: {{$price_desc}}</li>
        <li>Resort: {{$resort_name}}</li>
      </ul>

      </br>

      <p><strong>The customer's information summary:</strong></p>
      <ul>
        <li>Name: {{$user_name}}</li>
        <li>Email: {{$user_email}}</li>
        <li>Phone Number: {{$user_contact}}</li>
      </ul>

      </br>

      <p>Thank you for your cooperation, and we look forward to your positive response</br>
      regarding the reservation at {{$resort_name}}.
      </p>

      <p><strong>Note:</strong>Please open your QuickRent account to confirm or reject this reservation.</p>

      <p>Warm regards,</br>
        QuickRent Online
      </p>

    </div>
    
    <p>© 2023 QUICKRENT ONLINE</br>
    
  </body>
</html>