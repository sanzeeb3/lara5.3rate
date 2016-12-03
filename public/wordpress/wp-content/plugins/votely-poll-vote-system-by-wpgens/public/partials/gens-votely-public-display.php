<?php
/**
 * Provide a public-facing view for the plugin
 * @since      1.0.0
 */

?>

<div id="gens_votely_<?php echo $post->ID; ?>" class="gens_votely">
	<div class="gens_info">
		<span class="gens_title"><?php the_title(); ?></span>
		<?php if(!isset($votely_options["share-icons"])) { ?>
		<ul class="gens_votely_share_icon">
		  <li><a href="#"><i class="votelyicon-share"></i></a></li>
          <li><a class="gens_votely_fb" data-title="<?php echo $question; ?>" href="<?php the_permalink(); ?>#gens_votely_<?php echo $post->ID; ?>"><i class="votelyicon-facebook"></i></a></li>
          <li><a class="gens_votely_tw" data-title="<?php echo $question; ?>" href="<?php the_permalink(); ?>#gens_votely_<?php echo $post->ID; ?>"><i class="votelyicon-twitter"></i></a></li>
        </ul>
        <?php } ?>
	</div>

	<div class="gens_question">
		<div class="gens_outer">
			<h3><?php echo $question; ?></h3>
			<span class="gens_thanks<?php echo $voted; ?>"><?php echo $thanks_msg; ?></span> 
		</div>           
	</div>
	<div class="gens_answers <?php echo $square; ?>" data-vote="<?php echo $vote_msg; ?>">
		<div class="gens_first" style="background-color:<?php echo $votely_options['first-color']; ?>;">
			
			<a data-choice="first" href="#">
				<span style="top:<?php echo $first_percentage;?>px;" class="gens_overlay"></span>
				<span class="gens_answer" data-text="<?php echo $first_answer; ?>"><?php echo $first_answer; ?></span><span class="gens_votes"><?php echo $first_value; ?></span>
			</a>
		</div>
		<div class="gens_second" style="background-color:<?php echo $votely_options['second-color']; ?>">
			<a data-choice="second" href="#">
				<span style="top:<?php echo $second_percentage;?>px;" class="gens_overlay"></span>
				<span class="gens_answer" data-text="<?php echo $second_answer; ?>"><?php echo $second_answer; ?></span><span class="gens_votes"><?php echo $second_value; ?></span>
			</a>
		</div>
	</div>
</div>