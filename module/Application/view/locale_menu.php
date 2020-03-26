<?php
if (count($this->availableLocale) > 0) :

    echo '<ul class="dropdown-menu">';

    foreach ($this->availableLocale as $key => $value) :

        if ($key !== $this->currentLocale) {
            switch ($key) {

                case 'en_US':
                    echo '<li><a href="/application/locale/change-locale?locale=en_US"><img alt="" src="/images/flag/flag_uk.png">&nbsp;&nbsp;English</a></li>';
                    ;
                    break;
                case 'vi_VN':
                    echo '<li><a href="/application/locale/change-locale?locale=vi_VN"><img alt="" src="/images/flag/flag_vn.png">&nbsp;&nbsp;Tiếng Việt</a><li>';
                    break;
                case 'lo_LA':
                    echo '<li><a href="/application/locale/change-locale?locale=lo_LA"><img alt="" src="/images/flag/flag_la.png">&nbsp;&nbsp;ພາສາລາວ</a><li>';
                    break;
                case 'de_DE':
                    echo '<li><a href="/application/locale/change-locale?locale=de_DE"><img alt="" src="/images/flag/flag_germany.png">&nbsp;&nbsp;Deutsch</a><li>';
                    break;
            }
        }
    endforeach
    ;
    echo '</ul>';
 
endif;

?>

<!-- 
<ul class="dropdown-menu">


	<li><a
		href="<?php echo $this->baseUrl ?>/application/locale/change-locale?locale=en_US"><img
			alt="" src="/images/flag/flag_uk.png">&nbsp;&nbsp;English</a></li>
	<li><a
		href="<?php echo $this->baseUrl ?>/application/locale/change-locale?locale=vi_VN"><img
			alt="" src="/images/flag/flag_vn.png">&nbsp;&nbsp;Tiếng Việt</a>
	
	<li><a
		href="<?php echo $this->baseUrl ?>/application/locale/change-locale?locale=lo_LA"><img
			alt="" src="/images/flag/flag_la.png">&nbsp;&nbsp;ພາສາລາວ</a>
	
	<li><a
		href="<?php echo $this->baseUrl ?>/application/locale/change-locale?locale=de_DE"><img
			alt="" src="/images/flag/flag_germany.png">&nbsp;&nbsp;Deutsche</a>

</ul>
 -->

