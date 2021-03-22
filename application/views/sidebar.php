        <section>
            <h2>Latest Lottery News</h2>
                    <?php echo anchor($news_article_link, '+ News archive'); ?>
                    <?php echo article_links($recent_news); ?>
        </section>
            <div class="left-pad top-pad" style = "margin-right: 30px;">
                <div class="wrapper">
                <?php if ($sidebar_top) 
                        { 
                            /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                            echo "<div class='row' style = 'margin-left:5%;'>".stripslashes($sidebar_top->body)."</div>"; 
                            if(!is_null($sidebar_top->raw)) echo "<div class='row' style = 'margin-left:5%'>".stripslashes($sidebar_top->raw)."</div>"; 
                        } ?>
 
                    <?php if ($sidebar_middle) 
                        { 
                            /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                            echo "<div class='row' style = 'margin-left:5%;'>".$sidebar_middle->body."</div>";
                            if(!is_null($sidebar_middle->raw)) echo "<div class='row' style = 'margin-left:5%;'>".stripslashes($sidebar_middle->raw)."</div>"; 
                        } ?>
                    <?php if ($sidebar_bottom) 
                        { 
                            /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                            echo "<div class='row' style = 'margin-left:5%;'>".$sidebar_bottom->body."</div>";
                            if(!is_null($sidebar_top->raw)) echo "<div class='row' style = 'margin-left:5%;'>".stripslashes($sidebar_bottom->raw)."</div>"; 
                        }	?>
                </div>
            </div>