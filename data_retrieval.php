<?php
// Define the API endpoint to retrieve student enrollment data
$URL = 'https://data.gov.bh/api/explore/v2.1/catalog/datasets/01-statistics-of-students-nationalities_updated/records?where=colleges%20like%20%22IT%22%20AND%20the_programs%20like%20%22bachelor%22&limit=100';

// Fetch the API response
$response = file_get_contents($URL);

// Handle errors if data retrieval fails
if ($response === false) {
    die("Error fetching data from the API.");
}

// Decode the JSON response into a PHP array
$result = json_decode($response, true);

// Get the total number of records and the dataset rows
$totalCount = $result["total_count"];
$row = $result["results"];

// Define column labels for English and Arabic
$english = array(
    "year" => "Year",
    "semester" => "Semester",
    "the_programs" => "The Programs",
    "nationality" => "Nationality",
    "colleges" => "Colleges",
    "number_of_students" => "Number of Students"
);

$arabic = array(
    "number_of_students" => "عدد الطلبة",
    "lklyt" => "الكليات",
    "ljnsy" => "الجنسية",
    "lbrmj" => "البرامج",
    "lfsl_ldrsy" => "الفصل الدراسي",
    "year" => "السنة"
);

// Default language is English
$lang = $english;

// Check if a language parameter is provided via GET request
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['lang'])) {
        if ($_GET['lang'] == 'en') {
            $lang = $english;
        } elseif ($_GET['lang'] == 'ar') {
            $lang = $arabic;
        } else {
            $lang = 'error';
        }
    } else {
        $lang = 'error';
    }
}

// Redirect to English if language is invalid
if ($lang == 'error') {
    header("location: data_retrieval.php?lang=en");
    exit;
}

// Extract column keys from the selected language array
$columns = array_keys($lang);
$numColumns = count($columns);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of Bahrain Students Enrollment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <!-- Display page title based on selected language -->
        <h1>
            <?php 
            if ($_GET['lang'] == 'en') echo "University of Bahrain Students Enrollment by Nationality";
            else echo "طلبة جامعة البحرين المقيدين بحسب جنسياتهم";
            ?>
        </h1>
        <!-- Language switcher links -->
        <h6>
            <?php if ($_GET['lang'] == 'en'): ?>
                <a href="<?= "data_retrieval.php?lang=ar" ?>">العربية</a>
            <?php endif ?>
            <?php if ($_GET['lang'] == 'ar'): ?>
                <a href="<?= "data_retrieval.php?lang=en" ?>">English</a>
            <?php endif ?>
        </h6>
    </header>
    <main>
        <div class="overflow-auto">
            <!-- Table to display data -->
            <table>
                <thead data-theme="light">
                    <tr>
                        <!-- Table headers based on the selected language -->
                        <?php
                        for ($i = 0; $i < $numColumns; ++$i) {
                            echo "<th>" . htmlspecialchars($lang[$columns[$i]]) . "</th>"; 
                        }
                        ?>
                    </tr>
                </thead>
                <tbody data-theme="light">
                    <!-- Table rows dynamically generated from API data -->
                    <?php
                    for ($i = 0; $i < $totalCount; ++$i) {
                        echo '<tr>';
                        for ($j = 0; $j < $numColumns; ++$j) {
                            echo "<td>" . htmlspecialchars($row[$i][$columns[$j]]) . "</td>"; 
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
