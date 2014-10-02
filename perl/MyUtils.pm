#!/bin/echo This is a perl module and should not be run

package MyUtils;

use Date::Manip;

sub to_mysql($) {
	my($string)=@_;
	my($object)=Date::Manip::UnixDate($string,'%Y-%m-%d %T');
	if(!defined($object)) {
		die('bad convert for ['.$string.']');
	}
	return($object);
}

sub show_menu {
	my($stop)=0;
	my($result);
	while(!$stop) {
		for(my($i)=0;$i<@_;$i++) {
			my($entry)=$_[$i];
			print $entry."\n";
		}
		my($response);
		$response=<STDIN>;
		# handle EOF
		if(!defined($response)) {
			die('eof reached');
		}
		chomp($response);
		if(length($response)!=1) {
			next;
		}
		for(my($i)=0;$i<@_;$i++) {
			my($entry)=$_[$i];
			if($response eq substr($entry,0,1)) {
				$result=$response;
				$stop=1;
				next;
			}
		}
		if(!$stop) {
			print 'bad response'."\n";
		}
	}
	return $result;
}

sub show_yes_no_dialog($) {
	my($question)=$_[0];
	my($stop)=0;
	my($result);
	while(!$stop) {
		print STDOUT $question;
		flush STDOUT;
		my($response);
		$response=<STDIN>;
		chomp($response);
		if($response ne 'y' and $response ne 'n') {
			next;
		}
		$stop=1;
		if($response eq 'y') {
			$result=1;
		} else {
			$result=0;
		}
	}
	return $result;
}

sub get_from_user($) {
	my($message)=$_[0];
	print STDOUT $message;
	flush STDOUT;
	my($res);
	$res=<STDIN>;
	chomp($res);
	return $res;
}

sub delete_work($$) {
	my($dbh)=$_[0];
	my($f_id)=$_[1];
	$dbh->do('DELETE FROM TbWkWorkAuthorization WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkChapter WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkContrib WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkExternal WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkViewPerson WHERE viewId IN (SELECT id FROM TbWkWorkView WHERE workId=?)',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkView WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkReview WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWorkAlias WHERE workId=?',undef,$f_id);
	$dbh->do('DELETE FROM TbWkWork WHERE id=?',undef,$f_id);
}

return 1;
