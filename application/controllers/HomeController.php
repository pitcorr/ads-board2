<?php

class HomeController extends BaseController
{
    function indexAction()
    {
        $this->view('content/index');
    }

    function postListAction()
    {
        $data = array();

        $categories = (new Category())->getCategoriesBy(['id', 'title']);
//        var_dump($categories[0]['id']);
        $ads = (new Advertisement())->getAllAdvertisements();
//        $ads = (new Advertisement())->getAdvertisementsByCategory($categories[0]['id']);

        $data['categories'] = $categories;

        foreach ($ads as &$v) {
            $temp = strtotime($v['creationDate']);
            $v['creationDate'] = $temp;

            //get images from DB
            $imagesArray = (new AdvertisementImages())->getImagesByAdsId($v['id']);

            if(!is_null($imagesArray)) {
                $v['images'] = (new AdvertisementImages())->createImagePath($imagesArray, $_SESSION['userId'], $v['id']);
                $v['imagesPreview'] = (new AdvertisementImages())->createPreviewImagePath($imagesArray, $_SESSION['userId'], $v['id']);
            }
            else {
                $v['images'] = [];
                $v['imagesPreview'] = [];
            }
        }

        $data['advertisements'] = $ads;
        $this->view('content/postList', $data);
    }

    function adsLoadAction()
    {
        $catId = $_POST['catId'];

        $ads = (new Advertisement())->getAdvertisementsByCategory($catId);
//        ChromePhp::log($ads);


        foreach ($ads as &$v) {
            $temp = strtotime($v['creationDate']);
            $v['creationDate'] = $temp;

            //get images from DB
            $imagesArray = (new AdvertisementImages())->getImagesByAdsId($v['id']);

            if(!is_null($imagesArray)) {
                $v['images'] = (new AdvertisementImages())->createImagePath($imagesArray, $_SESSION['userId'], $v['id']);
                $v['imagesPreview'] = (new AdvertisementImages())->createPreviewImagePath($imagesArray, $_SESSION['userId'], $v['id']);
            }
            else {
                $v['images'] = [];
                $v['imagesPreview'] = [];
            }
        }
        if (!empty($ads)) {
            echo json_encode($ads);
        }
    }

    function pricingAction()
    {
        $this->view('content/pricing');
    }

    function postDetailAction()
    {
        try {
            $data = array();
            $id = $this->getParams('adsId');

            $ads = (new Advertisement())->getAdvertisementById($id);

            $imagesArray = (new AdvertisementImages())->getImagesByAdsId($id);

            if(!is_null($imagesArray)) {
                $ads[0]['images'] = (new AdvertisementImages())->createImagePath($imagesArray, $_SESSION['userId'], $id);
                $ads[0]['imagesPreview'] = (new AdvertisementImages())->createPreviewImagePath($imagesArray, $_SESSION['userId'], $id);

            }
            else {
                $ads[0]['images'] = [];
                $ads[0]['imagesPreview'] = [];
            }

            $data = $ads[0];

            $this->view('content/postDetail', $data);
        } catch (DatabaseErrorException $e) {
            $this->view('error/error', $data = array('message' => $e->getMessage()));
        }

    }

    function addPostAction()
    {
        $arr = Config::get('site');
        $tempUserDir = $arr['tempImagePath'] . $_SESSION['userId'];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'subject' => $subject = $_POST['subject'],
                'description' => $description = $_POST['description'],
                'price' => $price = floatval($_POST['price']),
                'creationDate' => date('Y-m-d H:m:s'),
                'categoryId' => $category = intval($_POST['category']),
                'userId' => intval($_SESSION['userId'])
            ];

            if (isset($subject) && isset($description) && isset($price) && isset($category)) {

                $adsId = (new Advertisement())->addAdvertisement($data);
                $userDir = $arr['imagePath'] . $_SESSION['userId'] . '/' . $adsId;
                $tempImages = glob($tempUserDir . '/*.{png,jpg}', GLOB_BRACE);

                //create folder for images + folder for images preview
                mkdir($userDir . '/preview', 0777, true);

                foreach ($tempImages as $image) {
                    $temp = explode('/', $image);
                    $imageName = end($temp);
                    $targetImageName = 'img_' . $_SESSION['userId'] . '_' . $adsId . '_' . $imageName;
                    $finalImageName = $userDir . '/' . $targetImageName;

                    $data = [
                        'imageName' => $targetImageName,
                        'advertisementId' => $adsId,
                    ];
                    (new AdvertisementImages())->saveAdsImages($data);

                    rename($image, $finalImageName);
                    (new AdvertisementImages())->makeThumb($finalImageName);
                }

                rmdir($tempUserDir);

                $this->redirect('/postlist');
            } else {
                $this->view('content/addPost');
            }

        } else {
            if (is_dir($tempUserDir)) {
                $this->rrmdir($tempUserDir);
            }
            $categories = (new Category())->getCategoriesBy(['id', 'title']);
            $data['categories'] = $categories;
            $this->view('content/addPost', $data);

        }
    }

    function termsAction()
    {
        $this->view('content/terms');
    }

    function aboutAction()
    {
        $this->view('content/about');
    }

    // for image download example
    // will be moved to correct controller
    function imageDownloadAction()
    {
        $arr = Config::get('site');

        $tempUserDir = $arr['tempImagePath'] . $_SESSION['userId'] . '/';

       if (!mkdir($tempUserDir, 0777, true))  ChromePhp::log("die");;
        $extension = explode('.', $_FILES['file']['name']);

        move_uploaded_file($_FILES['file']['tmp_name'], $tempUserDir . '/' . microtime(true) . '.' . end($extension));

        ChromePhp::log($_FILES);

    }

    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }


}
