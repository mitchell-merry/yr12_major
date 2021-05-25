<?php
	$sql = "SELECT * FROM genres";

	$genres = array();
	$t = mysqli_query($conn, $sql);
	while($row = mysqli_fetch_assoc($t))
	{
		array_push($genres, $row['genres_name']);
	}
?>
