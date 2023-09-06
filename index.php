<?php
    require_once("templates/header.php");
    require_once("dao/MovieDAO.php");

    $movieDao = new MovieDAO($conn, $BASE_URL);

   
    $latestMovies = $movieDao->getLatestMovies();
    $actionMovies = $movieDao->getMoviesByCategory("Ação");
    $comedyMovies = $movieDao->getMoviesByCategory("Comédia");
    $romanceMovies = $movieDao->getMoviesByCategory("Romance");
    $dramaMovies = $movieDao->getMoviesByCategory("Drama");
    $fantasyMovies = $movieDao->getMoviesByCategory("Fantasia | Ficção");
?>
    
    <div id="main-container" class="container-fluid">
        <h2 class="section-title">Filmes novos</h2>
        <p class="section-description">Veja as críticas dos últimos filmes adicionados no MovieStar</p>
        <div class="movies-container">
            <?php foreach($latestMovies as $movie): ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if(count($latestMovies) === 0): ?>
                <p class="empty-list">Ainda não há filmes cadastrados.</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title">Ação</h2>
        <p class="section-description">Veja os melhores filmes de ação</p>
        <div class="movies-container">
            <?php foreach($actionMovies as $movie): ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if(count($actionMovies) === 0): ?>
                <p class="empty-list">Ainda não há filmes de ação cadastrados.</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title">Comédia</h2>
        <p class="section-description">Veja os melhores filmes de comédia</p>
        <div class="movies-container">
            <?php foreach($comedyMovies as $movie): ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if(count($comedyMovies) === 0): ?>
                <p class="empty-list">Ainda não há filmes de comédia cadastrados.</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title">Romance</h2>
        <p class="section-description">Veja os melhores filmes de romance</p>
        <div class="movies-container">
            <?php foreach($romanceMovies as $movie): ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if(count($romanceMovies) === 0): ?>
                <p class="empty-list">Ainda não há filmes de romance cadastrados.</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title">Drama</h2>
        <p class="section-description">Veja os melhores filmes de drama</p>
        <div class="movies-container">
            <?php foreach($dramaMovies as $movie): ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if(count($dramaMovies) === 0): ?>
                <p class="empty-list">Ainda não há filmes de drama cadastrados.</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title">Fantasia | Ficção</h2>
        <p class="section-description">Veja os melhores filmes de fantasia | ficção</p>
        <div class="movies-container">
            <?php foreach($fantasyMovies as $movie): ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if(count($fantasyMovies) === 0): ?>
                <p class="empty-list">Ainda não há filmes de fantasia | ficção cadastrados.</p>
            <?php endif; ?>
        </div>
    </div>

<?php
    require_once("templates/footer.php");
?>    