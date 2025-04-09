<?php
// Include header
include_once 'includes/header.php';

// Process post submission if form was submitted
$postSubmitted = false;
$postError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_post'])) {
    // Validate and sanitize input
    $name = sanitizeInput($_POST['name']);
    $location = sanitizeInput($_POST['location']);
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    
    // Basic validation
    if (empty($name) || empty($location) || empty($title) || empty($content)) {
        $postError = true;
    } else {
        // Prepare post data
        $postData = [
            'name' => $name,
            'location' => $location,
            'title' => $title,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Save post to database
        $result = saveCommunityPost($postData);
        
        if ($result) {
            $postSubmitted = true;
        } else {
            $postError = true;
        }
    }
}

// Get community posts
$posts = getCommunityPosts(10);
?>

<!-- Page Header -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <h1 data-en="Farming Community" data-hi="कृषि समुदाय">
            <?php echo ($_SESSION['language'] == 'en') ? 'Farming Community' : 'कृषि समुदाय'; ?>
        </h1>
        <p class="lead" data-en="Connect with fellow farmers, share experiences, and learn together" data-hi="साथी किसानों से जुड़ें, अनुभव साझा करें, और एक साथ सीखें">
            <?php echo ($_SESSION['language'] == 'en') ? 'Connect with fellow farmers, share experiences, and learn together' : 'साथी किसानों से जुड़ें, अनुभव साझा करें, और एक साथ सीखें'; ?>
        </p>
    </div>
</section>

<!-- Community Forum Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Post submission alerts -->
                <?php if ($postSubmitted): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i data-feather="check-circle"></i>
                    <span data-en="Your post has been submitted successfully!" data-hi="आपकी पोस्ट सफलतापूर्वक सबमिट कर दी गई है!">
                        <?php echo ($_SESSION['language'] == 'en') ? 'Your post has been submitted successfully!' : 'आपकी पोस्ट सफलतापूर्वक सबमिट कर दी गई है!'; ?>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php elseif ($postError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i data-feather="alert-circle"></i>
                    <span data-en="There was an error submitting your post. Please try again." data-hi="आपकी पोस्ट सबमिट करने में एक त्रुटि हुई। कृपया पुनः प्रयास करें।">
                        <?php echo ($_SESSION['language'] == 'en') ? 'There was an error submitting your post. Please try again.' : 'आपकी पोस्ट सबमिट करने में एक त्रुटि हुई। कृपया पुनः प्रयास करें।'; ?>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <h2 data-en="Recent Community Posts" data-hi="हाल के समुदाय पोस्ट">
                    <?php echo ($_SESSION['language'] == 'en') ? 'Recent Community Posts' : 'हाल के समुदाय पोस्ट'; ?>
                </h2>
                
                <!-- Community posts -->
                <?php if (empty($posts)): ?>
                <div class="alert alert-info" role="alert">
                    <i data-feather="info"></i>
                    <span data-en="No community posts yet. Be the first to share your experience!" data-hi="अभी तक कोई समुदाय पोस्ट नहीं। अपना अनुभव साझा करने वाले पहले व्यक्ति बनें!">
                        <?php echo ($_SESSION['language'] == 'en') ? 'No community posts yet. Be the first to share your experience!' : 'अभी तक कोई समुदाय पोस्ट नहीं। अपना अनुभव साझा करने वाले पहले व्यक्ति बनें!'; ?>
                    </span>
                </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <div class="community-post">
                        <div class="post-header">
                            <div class="post-author"><?php echo $post['name']; ?></div>
                            <div class="post-location"><?php echo $post['location']; ?></div>
                        </div>
                        <h3 class="post-title"><?php echo $post['title']; ?></h3>
                        <div class="post-content">
                            <?php echo $post['content']; ?>
                        </div>
                        <div class="post-footer">
                            <div class="post-date">
                                <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?>
                            </div>
                            <div class="post-actions">
                                <div class="post-action">
                                    <i data-feather="thumbs-up"></i>
                                    <span data-en="Like" data-hi="पसंद">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Like' : 'पसंद'; ?>
                                    </span>
                                </div>
                                <div class="post-action">
                                    <i data-feather="message-square"></i>
                                    <span data-en="Comment" data-hi="टिप्पणी">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Comment' : 'टिप्पणी'; ?>
                                    </span>
                                </div>
                                <div class="post-action">
                                    <i data-feather="share-2"></i>
                                    <span data-en="Share" data-hi="शेयर">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Share' : 'शेयर'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <!-- Post submission form -->
                <div class="community-form sticky-top" style="top: 100px;">
                    <h3 class="form-title" data-en="Share Your Experience" data-hi="अपना अनुभव साझा करें">
                        <?php echo ($_SESSION['language'] == 'en') ? 'Share Your Experience' : 'अपना अनुभव साझा करें'; ?>
                    </h3>
                    <form id="community-post-form" method="post" action="">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label" data-en="Your Name" data-hi="आपका नाम">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Your Name' : 'आपका नाम'; ?>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" data-en="Please enter your name" data-hi="कृपया अपना नाम दर्ज करें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Please enter your name' : 'कृपया अपना नाम दर्ज करें'; ?>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="location" class="form-label" data-en="Location" data-hi="स्थान">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Location' : 'स्थान'; ?>
                            </label>
                            <input type="text" class="form-control" id="location" name="location" required
                                placeholder="<?php echo ($_SESSION['language'] == 'en') ? 'e.g., Punjab, Maharashtra' : 'जैसे, पंजाब, महाराष्ट्र'; ?>">
                            <div class="invalid-feedback" data-en="Please enter your location" data-hi="कृपया अपना स्थान दर्ज करें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Please enter your location' : 'कृपया अपना स्थान दर्ज करें'; ?>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="title" class="form-label" data-en="Post Title" data-hi="पोस्ट शीर्षक">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Post Title' : 'पोस्ट शीर्षक'; ?>
                            </label>
                            <input type="text" class="form-control" id="title" name="title" required
                                placeholder="<?php echo ($_SESSION['language'] == 'en') ? 'e.g., Success with New Irrigation Method' : 'जैसे, नई सिंचाई पद्धति के साथ सफलता'; ?>">
                            <div class="invalid-feedback" data-en="Please enter a title" data-hi="कृपया एक शीर्षक दर्ज करें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Please enter a title' : 'कृपया एक शीर्षक दर्ज करें'; ?>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="content" class="form-label" data-en="Your Experience" data-hi="आपका अनुभव">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Your Experience' : 'आपका अनुभव'; ?>
                            </label>
                            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                            <div class="invalid-feedback" data-en="Please share your experience" data-hi="कृपया अपना अनुभव साझा करें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Please share your experience' : 'कृपया अपना अनुभव साझा करें'; ?>
                            </div>
                        </div>
                        <button type="submit" name="submit_post" class="btn btn-primary w-100" data-en="Submit Post" data-hi="पोस्ट सबमिट करें">
                            <?php echo ($_SESSION['language'] == 'en') ? 'Submit Post' : 'पोस्ट सबमिट करें'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Farmer Stories Section -->
<section class="stories-section py-5">
    <div class="container">
        <div class="section-title">
            <h2 data-en="Success Stories" data-hi="सफलता की कहानियां">
                <?php echo ($_SESSION['language'] == 'en') ? 'Success Stories' : 'सफलता की कहानियां'; ?>
            </h2>
        </div>
        <div class="row">
            <?php
            // Get farmer stories
            $stories = getFarmerStories(3);
            
            foreach ($stories as $story):
            ?>
            <div class="col-md-4 mb-4">
                <div class="story-card">
                    <div class="story-header">
                        <div class="story-avatar">
                            <img src="/assets/<?php echo $story['image']; ?>" alt="<?php echo $story['name']; ?>" class="img-fluid">
                        </div>
                        <div class="story-meta">
                            <h4 class="story-name"><?php echo $story['name']; ?></h4>
                            <div class="story-location"><?php echo $story['location']; ?></div>
                            <div class="story-crop"><?php echo $story['crop']; ?></div>
                        </div>
                    </div>
                    <div class="story-quote"><?php echo $story['quote']; ?></div>
                    <div class="story-content"><?php echo $story['story']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Community Groups Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title">
            <h2 data-en="Farmer Groups" data-hi="किसान समूह">
                <?php echo ($_SESSION['language'] == 'en') ? 'Farmer Groups' : 'किसान समूह'; ?>
            </h2>
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <p class="lead text-center" data-en="Join specialized groups based on your farming interests" data-hi="अपनी कृषि रुचियों के आधार पर विशेष समूहों में शामिल हों">
                            <?php echo ($_SESSION['language'] == 'en') ? 'Join specialized groups based on your farming interests' : 'अपनी कृषि रुचियों के आधार पर विशेष समूहों में शामिल हों'; ?>
                        </p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i data-feather="droplet" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                                        <h4 class="mt-3" data-en="Organic Farming Group" data-hi="जैविक खेती समूह">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Organic Farming Group' : 'जैविक खेती समूह'; ?>
                                        </h4>
                                        <p data-en="Connect with 2,500+ organic farmers across India, share practices, and learn certification processes." data-hi="भारत भर में 2,500+ जैविक किसानों से जुड़ें, प्रथाओं को साझा करें, और प्रमाणन प्रक्रियाओं के बारे में जानें।">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Connect with 2,500+ organic farmers across India, share practices, and learn certification processes.' : 'भारत भर में 2,500+ जैविक किसानों से जुड़ें, प्रथाओं को साझा करें, और प्रमाणन प्रक्रियाओं के बारे में जानें।'; ?>
                                        </p>
                                        <button class="btn btn-outline-primary" data-en="Join Group" data-hi="समूह में शामिल हों">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Join Group' : 'समूह में शामिल हों'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i data-feather="trending-up" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                                        <h4 class="mt-3" data-en="Market Intelligence" data-hi="बाजार ख़ुफ़िया">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Market Intelligence' : 'बाजार ख़ुफ़िया'; ?>
                                        </h4>
                                        <p data-en="Get insights on market trends, cooperative selling strategies, and direct marketing opportunities." data-hi="बाजार के रुझानों, सहकारी बिक्री रणनीतियों और प्रत्यक्ष विपणन के अवसरों पर जानकारी प्राप्त करें।">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Get insights on market trends, cooperative selling strategies, and direct marketing opportunities.' : 'बाजार के रुझानों, सहकारी बिक्री रणनीतियों और प्रत्यक्ष विपणन के अवसरों पर जानकारी प्राप्त करें।'; ?>
                                        </p>
                                        <button class="btn btn-outline-primary" data-en="Join Group" data-hi="समूह में शामिल हों">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Join Group' : 'समूह में शामिल हों'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i data-feather="cloud-rain" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                                        <h4 class="mt-3" data-en="Climate-Resilient Farming" data-hi="जलवायु-लचीला खेती">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Climate-Resilient Farming' : 'जलवायु-लचीला खेती'; ?>
                                        </h4>
                                        <p data-en="Learn adaptation strategies for changing climate patterns, water conservation, and drought-resistant crops." data-hi="बदलते जलवायु पैटर्न, जल संरक्षण और सूखा प्रतिरोधी फसलों के लिए अनुकूलन रणनीतियों के बारे में जानें।">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Learn adaptation strategies for changing climate patterns, water conservation, and drought-resistant crops.' : 'बदलते जलवायु पैटर्न, जल संरक्षण और सूखा प्रतिरोधी फसलों के लिए अनुकूलन रणनीतियों के बारे में जानें।'; ?>
                                        </p>
                                        <button class="btn btn-outline-primary" data-en="Join Group" data-hi="समूह में शामिल हों">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Join Group' : 'समूह में शामिल हों'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i data-feather="smartphone" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                                        <h4 class="mt-3" data-en="Agri-Tech Enthusiasts" data-hi="कृषि-तकनीक उत्साही">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Agri-Tech Enthusiasts' : 'कृषि-तकनीक उत्साही'; ?>
                                        </h4>
                                        <p data-en="Discuss and share experiences with drones, IoT devices, farm management software, and other agricultural technologies." data-hi="ड्रोन, IoT उपकरणों, फार्म प्रबंधन सॉफ्टवेयर और अन्य कृषि प्रौद्योगिकियों के साथ अनुभवों पर चर्चा और साझा करें।">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Discuss and share experiences with drones, IoT devices, farm management software, and other agricultural technologies.' : 'ड्रोन, IoT उपकरणों, फार्म प्रबंधन सॉफ्टवेयर और अन्य कृषि प्रौद्योगिकियों के साथ अनुभवों पर चर्चा और साझा करें।'; ?>
                                        </p>
                                        <button class="btn btn-outline-primary" data-en="Join Group" data-hi="समूह में शामिल हों">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Join Group' : 'समूह में शामिल हों'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i data-feather="users" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                                        <h4 class="mt-3" data-en="Women Farmers Network" data-hi="महिला किसान नेटवर्क">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Women Farmers Network' : 'महिला किसान नेटवर्क'; ?>
                                        </h4>
                                        <p data-en="A dedicated space for women in agriculture to connect, share challenges, and discuss gender-specific farming innovations." data-hi="कृषि में महिलाओं के लिए एक समर्पित स्थान जहां वे जुड़ सकें, चुनौतियों को साझा कर सकें, और लिंग-विशिष्ट कृषि नवाचारों पर चर्चा कर सकें।">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'A dedicated space for women in agriculture to connect, share challenges, and discuss gender-specific farming innovations.' : 'कृषि में महिलाओं के लिए एक समर्पित स्थान जहां वे जुड़ सकें, चुनौतियों को साझा कर सकें, और लिंग-विशिष्ट कृषि नवाचारों पर चर्चा कर सकें।'; ?>
                                        </p>
                                        <button class="btn btn-outline-primary" data-en="Join Group" data-hi="समूह में शामिल हों">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Join Group' : 'समूह में शामिल हों'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i data-feather="activity" style="width: 48px; height: 48px; color: var(--primary-color);"></i>
                                        <h4 class="mt-3" data-en="Young Farmers Forum" data-hi="युवा किसान मंच">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Young Farmers Forum' : 'युवा किसान मंच'; ?>
                                        </h4>
                                        <p data-en="Connect with the next generation of farmers, discuss modern approaches, and build the future of Indian agriculture." data-hi="किसानों की अगली पीढ़ी से जुड़ें, आधुनिक दृष्टिकोणों पर चर्चा करें, और भारतीय कृषि का भविष्य बनाएं।">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Connect with the next generation of farmers, discuss modern approaches, and build the future of Indian agriculture.' : 'किसानों की अगली पीढ़ी से जुड़ें, आधुनिक दृष्टिकोणों पर चर्चा करें, और भारतीय कृषि का भविष्य बनाएं।'; ?>
                                        </p>
                                        <button class="btn btn-outline-primary" data-en="Join Group" data-hi="समूह में शामिल हों">
                                            <?php echo ($_SESSION['language'] == 'en') ? 'Join Group' : 'समूह में शामिल हों'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button class="btn btn-primary" data-en="See All Groups" data-hi="सभी समूह देखें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'See All Groups' : 'सभी समूह देखें'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2 data-en="Upcoming Farmer Events" data-hi="आगामी किसान कार्यक्रम">
                <?php echo ($_SESSION['language'] == 'en') ? 'Upcoming Farmer Events' : 'आगामी किसान कार्यक्रम'; ?>
            </h2>
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th data-en="Date" data-hi="तिथि">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Date' : 'तिथि'; ?>
                                </th>
                                <th data-en="Event" data-hi="कार्यक्रम">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Event' : 'कार्यक्रम'; ?>
                                </th>
                                <th data-en="Location" data-hi="स्थान">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Location' : 'स्थान'; ?>
                                </th>
                                <th data-en="Action" data-hi="कार्यवाही">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Action' : 'कार्यवाही'; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Dec 15, 2023</td>
                                <td data-en="National Farmer Innovation Summit" data-hi="राष्ट्रीय किसान नवाचार शिखर सम्मेलन">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'National Farmer Innovation Summit' : 'राष्ट्रीय किसान नवाचार शिखर सम्मेलन'; ?>
                                </td>
                                <td>New Delhi</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-en="Register" data-hi="पंजीकरण करें">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Register' : 'पंजीकरण करें'; ?>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Dec 20, 2023</td>
                                <td data-en="Organic Farming Workshop" data-hi="जैविक खेती कार्यशाला">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Organic Farming Workshop' : 'जैविक खेती कार्यशाला'; ?>
                                </td>
                                <td>Pune, Maharashtra</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-en="Register" data-hi="पंजीकरण करें">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Register' : 'पंजीकरण करें'; ?>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Jan 5, 2024</td>
                                <td data-en="Agricultural Technology Expo" data-hi="कृषि प्रौद्योगिकी प्रदर्शनी">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Agricultural Technology Expo' : 'कृषि प्रौद्योगिकी प्रदर्शनी'; ?>
                                </td>
                                <td>Bangalore, Karnataka</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-en="Register" data-hi="पंजीकरण करें">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Register' : 'पंजीकरण करें'; ?>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Jan 15, 2024</td>
                                <td data-en="Irrigation Systems Training" data-hi="सिंचाई प्रणाली प्रशिक्षण">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Irrigation Systems Training' : 'सिंचाई प्रणाली प्रशिक्षण'; ?>
                                </td>
                                <td>Ahmedabad, Gujarat</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-en="Register" data-hi="पंजीकरण करें">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Register' : 'पंजीकरण करें'; ?>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Feb 2, 2024</td>
                                <td data-en="Direct Farm Marketing Conference" data-hi="प्रत्यक्ष कृषि विपणन सम्मेलन">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Direct Farm Marketing Conference' : 'प्रत्यक्ष कृषि विपणन सम्मेलन'; ?>
                                </td>
                                <td>Chennai, Tamil Nadu</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-en="Register" data-hi="पंजीकरण करें">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Register' : 'पंजीकरण करें'; ?>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-primary" data-en="View All Events" data-hi="सभी कार्यक्रम देखें">
                        <?php echo ($_SESSION['language'] == 'en') ? 'View All Events' : 'सभी कार्यक्रम देखें'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once 'includes/footer.php';
?>
