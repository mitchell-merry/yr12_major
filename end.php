				</div></div>
				<div class="mstory-wrapper col-3 p-0" id="right">
					<div class="col-fixed bb" id="mstory-cf">
						<?php
							$sql = "SELECT * FROM stories
						 					LEFT JOIN images ON stories_minithumbnail = images_id
											WHERE stories_priority = 0
												AND stories.stories_status = 2
											ORDER BY stories_date DESC;";
							$result = mysqli_query($conn, $sql);
							$i = 0;
							while($row = mysqli_fetch_assoc($result))
							{
								$i++;

								$urlString = urlencode(substr(strtolower($row['stories_title']),0,40));
								$urlString = str_replace(".", "", $urlString);
								$urlString = str_replace("+", "-", $urlString);
								echo '<a class="mstory media w-100" id="mstory-'.$i.'" href="http://localhost/news/story/'.$row['stories_id'].'/'. $urlString .'">';
								echo '	<div class="media-left align-self-center">';
								// if($row['stories_minithumbnail'] != "")
								// {
									echo '		<img class="mstory-img bg-danger media-object" src="/news/imgs/'.$row['images_path'].'">'; //src="\news\imgs\mini_thumbnail\5c7c93ca5f5e58.80605389.png">
								// }
								// else
								// {src="\news\imgs\mini_thumbnail\5c7c93ca5f5e58.80605389.png"
								// }
								echo '	</div>';
								echo '	<div class="media-body align-self-center my-2 mr-3">';
								echo '		<div class="mstory-title text-light">'.$row['stories_title'].'</div>';
								echo '		<div class="mstory-subtitle text-light">'.$row['stories_subtitle'].'</div>';
								echo '	</div>';
								echo '</a>';
							}
						?>
					</div>
				</div>
				<script src="/news/includes/scroll.inc.js"></script>
				<script src="/news/includes/resize.inc.js"></script>
				<script src="/news/includes/click.inc.js"></script>
			</div>
		</div>
	</body>
</html>
