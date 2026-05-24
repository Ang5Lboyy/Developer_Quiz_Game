<?php 
$page_title = "Developer Quiz - Home";
include "header.php"; 
?>

<div class="container">
    <div class="slider">
        <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1200&auto=format&fit=crop" class="slide active">
        <img src="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=1200&auto=format&fit=crop" class="slide">
        <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?q=80&w=1200&auto=format&fit=crop" class="slide">
        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1200&auto=format&fit=crop" class="slide">
        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1200&auto=format&fit=crop" class="slide">
        <img src="https://www.excelsior.edu/wp-content/uploads/2025/01/computer-programming-vs-computer-science-image_blog.jpg" class="slide">
        <img src="https://www.adveits.com/wp-content/uploads/2023/11/what-is-programming-and-why-is-it-important.jpg" class="slide">
        <img src="https://assets.techrepublic.com/uploads/2018/09/istock-869356606.jpg" class="slide">
        <img src="https://jessup.edu/wp-content/uploads/2023/12/Programming-in-Computer-Science-NEW.webp" class="slide">
        <img src="https://www.theforage.com/blog/wp-content/uploads/2023/03/programming-skills-scaled.jpg" class="slide">
        <img src="https://winatalent.com/blog/wp-content/uploads/2023/12/Best-Programming-Software-for-Writing-Code.jpg" class="slide">

    </div>

    <h1>💻 <?php echo t("title"); ?></h1>
    <p><?php echo t("desc"); ?></p>

     <a class="button start" href="quiz_setup.php?lang=<?php echo $lang; ?>">▶ <?php echo t("start"); ?></a>
    <a class="button admin" href="admin.php?lang=<?php echo $lang; ?>">🔐 <?php echo t("admin"); ?></a>
    <a class="button" href="highscores.php?lang=<?php echo $lang; ?>" style="background: linear-gradient(135deg, #ffa500, #ff6b4a);">🏆 <?php echo t("high_scores"); ?></a>

</div>

<script>
const slides = document.querySelectorAll('.slide');
let current = 0;
setInterval(() => {
    slides[current].classList.remove('active');
    current = (current + 1) % slides.length;
    slides[current].classList.add('active');
}, 3000);
</script>

<?php include "footer.php"; ?>