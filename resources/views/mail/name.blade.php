<!doctype html>
<html lang="en">

  <body>

    <div>
      <h1>Email Verification</h1>
      <p>Hello {{$user->name}}</p>
      <p>Please click the below button to verify your email address</p>

      <a href="{{ URL::temporarySignedRoute('verification.verify, now()->addMinutes(30), ['id' => $user->id)}}" class="button button-primary">
        Verify Email Address 
      </a>

      <!-- <button type="button">Click to verify</button> -->

    </div>

  </body>

</html>