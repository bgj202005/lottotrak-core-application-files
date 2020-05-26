        <section>
            <h2>Latest Lottery News</h2>
                    <?php // echo anchor($news_article_link, '+ News archive'); ?>
                    <?php echo anchor($news_article_link, '+ News archive'); ?>
                    <?php echo article_links($recent_news); ?>
        </section>
            <div class="left-pad top-pad" style = "margin-right: 30px;">
                <div class="wrapper">
                <?php if ($sidebar_top) 
                        { 
                            /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                            echo "<div class='row' style = 'margin-left:5%'>".$sidebar_top->body."</div>"; 
                        } ?>
                    <!-- <a href="#"><img class = "home" src="<?php echo base_url() ?>images/banner-1.jpg" alt=""></a> 
                    <a href="#"><img class = "home" src="<?php echo base_url() ?>images/banner-2.jpg" alt=""></a> -->
                    <?php if ($sidebar_middle) 
                        { 
                            /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                            echo "<div class='row' style = 'margin-left:5%'>".$sidebar_middle->body."</div>"; 
                        } ?>
                    <?php if ($sidebar_bottom) 
                        { 
                            /* echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>"; */
                            echo "<div class='row' style = 'margin-left:5%'>".$sidebar_bottom->body."</div>"; 
                        }	?>    
                </div>
                <!-- <a class="link" href="#">More propositions</a> -->
            </div>
