<?php 
use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $meta_title;
?>

<div class="wrapper" style="">
    
    <div class="head">
        <h2>Каталог компаний</h2>
    </div>
    
    <div class="company-detail__info-container">
        <div class="company-detail__info-column">
            <div class="company-detail__info-block wrap">
                <p class="company-detail__opt-logo-p">
                <img class="company-detail__opt-logo-img" src="<?=$company['image']?>" />
                </p>
<?php /*                <p class="company-detail__opt-phone"><?=$company['phone']?></p> */ ?>
                <a class="company-detail__opt-website" href="<?=$company['website']?>" target="_blank"><?=$company['website']?></a>
<?php /*
                <a class="company-detail__opt-email" href="mailto:<?=$company['email']?>"><?=$company['email']?></a>
*/ ?>
            </div>
            
            <div class="company-detail__company-callback-btn">
                <a class="company-detail__company-callback-btn-a btn blue popup_btn" rel="#company_callback"><span class="company-detail__company-callback-btn-span">Связаться с компанией</span></a>
            </div>
            
            <div class="company-detail__info-options">
                <p><span class="company-detail__info-opt-title">Вид организации: </span><br><span class="company-detail__info-opt-value">Коллекторское агенство</span></p>
                <br>

                <?if ($company['inn']) { ?>
                <p><span class="company-detail__info-opt-title">ИНН </span><span class="company-detail__info-opt-value"><?=$company['inn']?></span></p>
                <? } ?>
                <?if ($company['ogrn']) { ?>
                <p><span class="company-detail__info-opt-title">ОГРН </span><span class="company-detail__info-opt-value"><?=$company['ogrn']?></span></p>
                <? } ?>
                <br>
                
                <?if ($company['fssp']) { ?>
                <p><span class="company-detail__info-opt-title">Член реестра ФССП:</span><span class="company-detail__info-opt-value"><?if ($company['fssp']) {?> да <? } else { ?> нет <? } ?></span></p>
                <? } ?>
                
                <? if (isset($company['member_napki_2022'])) { ?>
                <p><span class="company-detail__info-opt-title">Членство НАПКА:</span><span class="company-detail__info-opt-value"><?if ($company['member_napki_2022']) {?> да <? } else { ?> нет <? } ?></span></p>
                <? } ?>
                
                <? if ($company['fssp'] || isset($company['member_napki_2022'])) { ?>
                <br>
                <? } ?>
                
                <?if ($company['okved']) { ?>
                <p><span class="company-detail__info-opt-title">Основной вид деятельности по ОКВЭД: </span><span class="company-detail__info-opt-value"><?=$company['okved']?></span></p>
                <br>
                <? } ?>
                <?if ($company['amount_staff']) { ?>
                <p><span class="company-detail__info-opt-title">Кол-во персонала: </span><span class="company-detail__info-opt-value"><?=$company['amount_staff']?> чел.</span></p>
                <? } ?>
                <?if (is_numeric($company['rating']) && $company['rating'] !== 0) { ?>
                <p><span class="company-detail__info-opt-title">Рейтинг ПКО-300: </span><span class="company-detail__info-opt-value"><?=$company['rating']?></span></p>
                <? } ?>
            </div>
            
        </div>
    </div>
    
    <div class="company-detail__text-column">
        <div class="company-detail__description-container">
            <div class="company-detail__title-block wrap"><?=$company['name']?></div>
            <div class="company-detail__text-block wrap"><?=$company['detail_text']?></div>
            
            <?if ($company['show_fin_2022'] || $company['show_fin_2019']) { ?>
            <div class="company-detail__fin-block wrap">
                <?if ($company['show_fin_2022']) { ?>
                <div class="company-detail__fin-item">
                    <p class="company-detail__fin-title">Финансовые показатели за 2022 г. по данным ФНС</p>
                    
                    <?if ($company['revenue_2022']) { ?>
                    <p class="company-detail__fin-row"><span class="company-detail__fin-row-title">Выручка: </span><span class="company-detail__fin-row-value"><?=number_format($company['revenue_2022'], 0, ".", " ")?></span></p>
                    <? }?>
                    <?if ($company['profit_2022']) { ?>
                    <p class="company-detail__fin-row"><span class="company-detail__fin-row-title">Прибыль: </span><span class="company-detail__fin-row-value"><?=number_format($company['profit_2022'], 0, ".", " ")?></span></p>
                    <? }?>
                    <?if ($company['debt_obligations_2019']) { ?>
                    <p class="company-detail__fin-row"><span class="company-detail__fin-row-title">Долговые обязательства: </span><span class="company-detail__fin-row-value"><?=number_format($company['debt_obligations_2019'], 0, ".", " ")?></span></p>
                    <? }?>
                </div>
                <? }?>
            </div>
            <? }?>
        </div>
        
        <?php
        /*
            if ($company['company_id'] == 7) {
                $company['posts'] = [
                    ['post_id'=>'1731', 'title'=>'Первое коллекторское бюро оштрафовано за нарушения при взыскании долгов', 'url'=>'/news/1731'],
                ];
            }
         */ 
        ?>
            
        <?php if (!empty($company['posts'])) { ?>
        <div class="company-detail__posts-block wrap">
            <div class="company-detail__posts-title">Статьи</div>
            <div class="company-detail__posts">
            <?php foreach ($company['posts'] as $post) { ?>
                <div class="company-detail__posts-item">
                    <div class="company-detail__posts-item-image"><a class="company-detail__posts-item-image-a" href="<?php echo $post['url']?>"><img class="company-detail__posts-item-image-img" src="<?php echo $post['thumbnail']?>" alt=""></a></div>
                    <div class="company-detail__posts-item-title"><a class="company-detail__posts-item-title-a" href="<?php echo $post['url']?>"><?php echo $post['title']?></a></div>
                    <div class="company-detail__posts-item-date"><?php echo $post['date']?></div>
                </div>
            <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <div class="clr"></div>
    
    
</div>