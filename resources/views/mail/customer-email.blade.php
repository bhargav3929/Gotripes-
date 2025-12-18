
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Information</title>
</head>
<body >
    <h2>Applicant Information</h2>
    
    <?php if (array_key_exists('name', $data)) : ?>
        <p><strong>Applicant Name:</strong> <?php echo $data['name']; ?></p>
    <?php endif; ?>
<?php if (array_key_exists('firstname', $data)) : ?>
        <p><strong>Applicant FirstName:</strong> <?php echo $data['firstname']; ?></p>
    <?php endif; ?>

    <?php if (array_key_exists('lastname', $data)) : ?>
        <p><strong>Applicant LastName:</strong> <?php echo $data['lastname']; ?></p>
    <?php endif; ?>

    <?php if (array_key_exists('phone', $data)) : ?>
        <p><strong>Applicant PhNo:</strong> <?php echo $data['phone']; ?></p>
    <?php endif; ?>
<?php if (array_key_exists('phonenumber', $data)) : ?>
        <p><strong>Applicant PhNo:</strong><?php echo $data['countryCode']; ?> <?php echo $data['phonenumber']; ?></p>
    <?php endif; ?>

    <?php if (array_key_exists('email', $data)) : ?>
        <p><strong>Applicant Email:</strong> <?php echo $data['email']; ?></p>
    <?php endif; ?>

    <?php if (array_key_exists('course', $data)) : ?>
        <p><strong>Applicant Course Selected:</strong> <?php echo $data['course']; ?></p>
    <?php endif; ?>

    <?php if (array_key_exists('subject', $data)) : ?>
        <p><strong>Applicant Subject:</strong> <?php echo $data['subject']; ?></p>
    <?php endif; ?>

    <?php if (array_key_exists('message', $data)) : ?>
        <p><strong>Message:</strong> <?php echo $data['message']; ?></p>
    <?php endif; ?>
</body>
</html>

