<?php 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<form method="GET" action="" id="search_form">
    <input type="text" class="input-xlarge" name="query" id="suggest" autocomplete="off" 
        placeholder = "hledej" value="<?=isset($_GET['query'])?htmlentities($_GET['query']):''?>"> 
     <input type="submit" class="btn btn-primary"
	id="send" name="send" value="Vyhledat">
</form>
<p class="lead">
    <?php if(isset($docs[count((array) ($docs))][1])):?>
        Nalezeno položek : <?=$docs[count((array) ($docs))][1]?>
    <?php
    if ($docs[count((array) ($docs))][1] == 1) {
        $product_link = (JURI::root().'?option=com_virtuemart&view=productdetails&virtuemart_product_id='.($docs[0] ["virtuemart_product_id"]).'&virtuemart_category_id='.($docs[0] ["virtuemart_category_id"]));
        header('Location: '.$product_link);
        header('Connection: close');
    }
    endif;?>
       
</p>     
<div class="row"><div class="span" style="display: none;"></div>
    <?php if (count((array)($docs)) > 0): ?>
        <div class="span9"><?php require_once dirname(__FILE__) . '/paginator.php';?></div>
	<?php $i = 0; foreach ($docs as $doc): 
            $product_id = $doc["virtuemart_product_id"];
            $product_name = $doc['product_name'];
            $category_id = $doc['virtuemart_category_id'];?>
            <div class="span9">
		<div class="container">
                <?php if (!$product_id) continue;?>
            <form method="post" class="product js-recalculate" action="#">
            <div class="addtocart-bar">
                
                <div class="main-image">
                    <div class="product-details-imege-handler">
                <?php $image_link = JUri::root() .'images/stories/virtuemart/product/resized/'.$doc['file_url'];?>
            <img src=<?=$image_link;?>>
                    </div>
                    <div class="clear"></div>
                </div>

                
      <?php			
//            $product_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$doc['virtuemart_product_id'].'&virtuemart_category_id='.$doc['virtuemart_category_id']);
              $product_link = (JURI::root().'?option=com_virtuemart&view=productdetails&virtuemart_product_id='.($doc['virtuemart_product_id']).'&virtuemart_category_id='.($doc['virtuemart_category_id']));
            ?>    
            <a href="<?=$product_link; ?>"><?= $doc['product_name']?></a>
            <br />
            <?='<strong>dostupnost : '.$doc['product_availability'].'</strong>' ?>
            <br />
            <?= "<i>Vaše cena : ".substr($doc['product_price'],0,-4)." Kc bez DPH </i>"?>    

            <span class="quantity-box">
                <input class='input-mini' type='number' name="quantity[]" value='1'>
            </span>
                <span class="quantity-controls js-recalculate">
                <span class="quantity-controls quantity-plus"></span>
                <span class="quantity-controls quantity-minus"></span>
            </span>
            <input type="submit" name="addtocart" class="addtocart-button" value="Do košíku" title="Do košíku">
            <input type="hidden" name="virtuemart_product_id[]" value=<?=$product_id?>>
            <noscript><input type="hidden" name="task" value="add"/></noscript> 

            <br/>
            <hr>
            </div>	
            <input type="hidden" name="option" value="com_virtuemart">
            <input type="hidden" name="view" value="cart">
            <input type="hidden" name="virtuemart_product_id[]" value=<?=$product_id?>>
            <input type="hidden" name="pname" value=<?=$product_name?>>
            <input type="hidden" name="pid" value=<?=$product_id?>>
            <input type="hidden" name="Itemid" value=<?=$category_id?>>
            </form>
                    </div>
            </div>

                
                
	<?php $i++; endforeach; ?>
        <div class="span9"><?php require dirname(__FILE__) . '/paginator.php';?></div>
    	<?php elseif (isset($_GET['query']) && $_GET['query'] != ''): ?>
            <p>Nenalezeno !</p>
	<?php endif; ?>
</div>
