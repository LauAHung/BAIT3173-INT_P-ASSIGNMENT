<!DOCTYPE html>
<html>
<body>
  <h2>{{ $user && isset($user->first_name) ? 'Hi ' . $user->first_name . ',' : 'Hello,' }}</h2>
  <div>{!! nl2br(e($content)) !!}</div>
  <p style="margin-top:16px; color:#666;">— TravelFree Team</p>
</body>
</html>


