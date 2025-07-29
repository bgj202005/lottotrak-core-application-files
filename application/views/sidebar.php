        <section class="sidebar-section">
            <h2 class="sidebar-title">Latest Lottery News</h2>
            <div class="news-archive-link">
                <?php echo anchor($news_article_link, '+ News archive', 'class="btn btn-outline-light btn-sm"'); ?>
            </div>
            <div class="news-links">
                <?php echo article_links($recent_news); ?>
            </div>
        </section>
        <div class="sidebar-content">
            <div class="wrapper">
            <?php if ($sidebar_top) 
                    { 
                        /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                        echo "<div class='sidebar-section'>".stripslashes($sidebar_top->body)."</div>"; 
                        if(!is_null($sidebar_top->raw)) echo "<div class='sidebar-section'>".stripslashes($sidebar_top->raw)."</div>"; 
                    } ?>

                <?php if ($sidebar_middle) 
                    { 
                        /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                        echo "<div class='sidebar-section'>".$sidebar_middle->body."</div>";
                        if(!is_null($sidebar_middle->raw)) echo "<div class='sidebar-section'>".stripslashes($sidebar_middle->raw)."</div>"; 
                    } ?>
                <?php if ($sidebar_bottom) 
                    { 
                        /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                        echo "<div class='sidebar-section'>".$sidebar_bottom->body."</div>";
                        if(!is_null($sidebar_top->raw)) echo "<div class='sidebar-section'>".stripslashes($sidebar_bottom->raw)."</div>"; 
                    }	?>
            </div>
        </div>