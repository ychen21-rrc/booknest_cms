<?php
require 'includes/db.php';
session_start();

// Check if user is logged in to pre-fill form
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? $_SESSION['user']['username'] : '';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields (Name, Email, and Message).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Prepare email content
        $to = 'ychen21@rrc.ca';
        $subject = 'BookNest Contact Form - Message from ' . $name;
        
        $email_body = "
        <html>
        <head>
            <title>BookNest Contact Form Submission</title>
        </head>
        <body>
            <h2>New Contact Form Submission from BookNest</h2>
            <hr>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
            
        if (!empty($phone)) {
            $email_body .= "<p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>";
        }
        
        $email_body .= "
            <p><strong>Message:</strong></p>
            <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            <hr>
            <p><em>This message was sent from the BookNest website contact form.</em></p>
            <p><em>Sent on: " . date('F j, Y \a\t g:i A') . "</em></p>
        </body>
        </html>";
        
        // Email headers
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: BookNest Website <noreply@booknest.com>',
            'Reply-To: ' . $email,
            'X-Mailer: PHP/' . phpversion()
        );
        
        // Try to send email
        if (mail($to, $subject, $email_body, implode("\r\n", $headers))) {
            $success = 'Thank you for your message! We have received your inquiry and will respond to you at ' . htmlspecialchars($email) . ' as soon as possible.';
            // Clear form data on success
            $name = $email = $phone = $message = '';
        } else {
            $error = 'Sorry, there was an error sending your message. Please try again later or contact us directly at ychen21@rrc.ca.';
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="contact-header text-center mb-5">
                <h1 class="contact-title">Get In Touch</h1>
                <div class="title-underline"></div>
                <p class="contact-subtitle">
                    Have questions about our book reviews or want to suggest a book? 
                    We'd love to hear from you!
                </p>
            </div>

            <!-- Success/Error Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Contact Form Card -->
            <div class="contact-card">
                <div class="contact-card-body">
                    <form method="post" class="contact-form">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       placeholder="Your full name"
                                       value="<?= htmlspecialchars($name ?? ($_POST['name'] ?? '')) ?>"
                                       required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    Phone Number <span class="text-muted">(optional)</span>
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="(123) 456-7890"
                                       value="<?= htmlspecialchars($phone ?? ($_POST['phone'] ?? '')) ?>">
                            </div>
                            
                            <div class="col-12">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="your.email@example.com"
                                       value="<?= htmlspecialchars($email ?? ($_POST['email'] ?? '')) ?>"
                                       required>
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">
                                    Message <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="message" 
                                          name="message" 
                                          rows="6" 
                                          placeholder="Tell us about your project or inquiry..."
                                          required><?= htmlspecialchars($message ?? ($_POST['message'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions mt-4">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="bi bi-send me-2"></i>Submit
                            </button>
                            <button type="reset" class="btn btn-outline-secondary btn-reset">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="contact-info mt-5">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <h5>Email Us</h5>
                            <p class="text-muted">
                                <a href="mailto:ychen21@rrc.ca" class="text-decoration-none">
                                    ychen21@rrc.ca
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <h5>Response Time</h5>
                            <p class="text-muted">We typically respond within 24-48 hours</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="bi bi-book-fill"></i>
                            </div>
                            <h5>BookNest Team</h5>
                            <p class="text-muted">Passionate about books and reading</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-header {
    margin-bottom: 3rem;
}

.contact-title {
    color: var(--dark-blue);
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.title-underline {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--coral), #ff9999);
    margin: 0 auto 2rem;
    border-radius: 2px;
}

.contact-subtitle {
    font-size: 1.2rem;
    color: #666;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.contact-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.contact-card-body {
    padding: 3rem;
}

.contact-form .form-label {
    color: var(--dark-blue);
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.contact-form .form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.contact-form .form-control:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 0.2rem rgba(255, 123, 123, 0.15);
    background-color: white;
    transform: translateY(-2px);
}

.contact-form textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
    align-items: center;
}

.btn-submit {
    background: linear-gradient(135deg, var(--coral), #ff9999);
    border: none;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 123, 123, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 123, 123, 0.4);
    background: linear-gradient(135deg, #ff6b6b, var(--coral));
}

.btn-reset {
    border: 2px solid #6c757d;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.contact-info {
    background: var(--light-gray);
    border-radius: 20px;
    padding: 3rem 2rem;
}

.contact-info-item {
    padding: 1.5rem;
    border-radius: 15px;
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
    height: 100%;
}

.contact-info-item:hover {
    transform: translateY(-5px);
}

.contact-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--coral), #ff9999);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.contact-info-item h5 {
    color: var(--dark-blue);
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.alert {
    border-radius: 15px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

@media (max-width: 768px) {
    .contact-card-body {
        padding: 2rem 1.5rem;
    }
    
    .contact-title {
        font-size: 2.5rem;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-submit, .btn-reset {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .contact-info {
        padding: 2rem 1rem;
    }
}
</style>

<?php include 'footer.php'; ?>