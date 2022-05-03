<?php
/**
 * timeline 的内容部分
 *
 */
?>

<?php
    $show_month = get_option('argon_archives_timeline_show_month', 'true');
    $POST = $GLOBALS['post'];
    echo "<div class='argon-timeline archive-timeline'>";
    $last_year = 0;
    $last_month = 0;
    $posts = get_posts('numberposts=-1&orderby=post_date&order=DESC');
    foreach ($posts as $post){
        setup_postdata($post);
        $year = mysql2date('Y', $post -> post_date);
        $month = mysql2date('M', $post -> post_date);
        if ($year != $last_year){
            echo "<div class='argon-timeline-node'>
                    <h2 class='argon-timeline-time archive-timeline-year'><a href='" . get_year_link($year) . "'>" . $year . "</a></h2>
                    <div class='argon-timeline-card card bg-gradient-secondary archive-timeline-title'></div>
                </div>";
                $last_year = $year;
                $last_month = 0;
        }
        if ($month != $last_month && $show_month == 'true'){
            echo "<div class='argon-timeline-node'>
                    <h3 class='argon-timeline-time archive-timeline-month" . ($last_month == 0 ? " first-month-of-year" : "") . "'><a href='" . get_month_link($year, $month) . "'>" . $month . "</a></h3>
                    <div class='argon-timeline-card card bg-gradient-secondary archive-timeline-title'></div>
                </div>";
                $last_month = $month;
        } ?>
        <div class='argon-timeline-node'>
            <div class='argon-timeline-time'><?php echo mysql2date('m-d', $post -> post_date); ?></div>
            <div class='argon-timeline-card card bg-gradient-secondary archive-timeline-title'>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </div>
        </div>
        <?php
    }
    echo '</div>';
    $GLOBALS['post'] = $POST;