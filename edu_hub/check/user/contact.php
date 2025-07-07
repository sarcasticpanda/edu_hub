<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St. Xavier's College - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e6ecf4 0%, #c9d6e8 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        .header-crest {
            width: 80px;
            height: 80px;
            background: url('../images/school.png') no-repeat center;
            background-size: contain;
            margin: 0 auto 1rem;
        }
        .contact-section {
            background-color: #ffffff;
            padding: 4rem 3rem;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            margin-top: 3rem;
            transition: transform 0.3s ease;
        }
        .contact-section:hover {
            transform: translateY(-5px);
        }
        h1 {
            color: #1a252f;
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            letter-spacing: 1px;
        }
        .contact-info {
            background: linear-gradient(135deg, #f9fbfd 0%, #eef2f7 100%);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2.5rem;
        }
        .contact-info p {
            color: #2d3e50;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        .contact-info i {
            color: #2c82d9;
            font-size: 1.3rem;
            margin-right: 1.2rem;
            transition: color 0.3s ease;
        }
        .contact-info p:hover i {
            color: #1e5ea0;
        }
        .contact-form label {
            color: #1a252f;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .contact-form input, .contact-form textarea {
            border: 2px solid #e0e6ed;
            border-radius: 12px;
            padding: 12px 15px;
            width: 100%;
            margin-bottom: 1.5rem;
            background: #ffffff;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-form input:focus, .contact-form textarea:focus {
            border-color: #2c82d9;
            box-shadow: 0 0 10px rgba(44, 130, 217, 0.2);
            outline: none;
        }
        .contact-form button {
            background: linear-gradient(135deg, #2c82d9 0%, #1e5ea0 100%);
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 130, 217, 0.3);
        }
        @media (max-width: 900px) {
            .contact-section { padding: 2.5rem 1.2rem; }
            .contact-info { padding: 1.2rem; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>
    <main style="margin-top: 60px;">
    <section class="contact-section">
        <div class="container mx-auto px-4">
            <div class="header-crest"></div>
            <h1>Contact Us</h1>
            <!-- Contact Information -->
            <div class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i> St. Xavier's College, 5 Mahapalika Marg, Mumbai, Maharashtra 400001, India</p>
                <p><i class="fas fa-phone"></i> Phone: +91 22 2262 0662</p>
                <p><i class="fas fa-envelope"></i> Email: info@stxavierscollege.edu</p>
            </div>
            <!-- Contact Form -->
            <div class="contact-form">
                <form action="#" method="POST">
                    <div class="mb-4">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label">Your Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </section>
    </main>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 