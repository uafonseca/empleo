<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Blog;
use Symfony\Component\HttpFoundation\Request;
use FeedIo\Factory\Builder\MonologBuilder;


class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog_")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $urls = $em->getRepository('App:FeedUrl')->findByEnabledField(true);
        $posts = array();
        $errors = "";
        $array = $em->getRepository(Blog::class)->findAll();
        foreach ($array as $item) {
            $post = new Blog();
            $post->setUrl($this->generateUrl('blog_show',['id'=>$item->getId()]));
            $post->setTitle($item->getTitle());
            $post->setText($item->getText());
            $post->setCreated($item->getCreated());
            $post->setImage($item->getImage());
            $post->setLocal('local');
            $post->setId($item->getId());
            $post->setUser($item->getUser());

            $posts[] = $post;
        }
        if (count($urls) > 1) {
            $requests = array();
            foreach ($urls as $dir) {
                $requests[] = new \FeedIo\Async\Request($dir->getUrl());
            }
            $feedIo = new \FeedIo\FeedIo( new \FeedIo\Adapter\Guzzle\Client(new \GuzzleHttp\Client()), (new MonologBuilder())->getLogger());
            $feedIo->readAsync($requests,  new \FeedIo\Async\DefaultCallback((new MonologBuilder())->getLogger()));
            for ($i = 0; $i < count($requests); $i++) {
                $result = $feedIo->read($requests[$i]);
                foreach ($result->getFeed() as $item) {
                    $post = new Blog();
                    $post->setUrl($this->generateUrl('blog_show',['id'=>$item->getLink()]));
                    $post->setTitle($item->getTitle());
                    $post->setText($item->getDescription());
                    $post->setCreated($item->getLastModified()->format(\DateTime::ATOM));
                    $post->setLocal('rss');
                    $posts[] = $post;
                }
            }

        }
        if (count($urls) == 1) {
            try {
                $url = $urls[0]->getUrl();
                $days = $urls[0]->getDaysToUpdate();
                $feedIo = $this->container->get('feedio');
                $result = $feedIo->readSince($url, new \DateTime("-$days days"));
                $i=0;
                foreach ($result->getFeed() as $item) {
//                    foreach($item->getMedias() as $media) {
//                        var_dump($media);
//                    }
                    $post = new Blog();
                    $post->setUrl($this->generateUrl('blog_show',['id'=>$url,'index'=>$i++]));
                    $post->setTitle($item->getTitle());
                    $post->setText($item->getDescription());
                    $post->setCreated($item->getLastModified()->format(\DateTime::ATOM));
                    $post->setLocal('rss');
//                    $post->setUser($item->getUserAgent());
                    $posts[] = $post;
                }
            } catch (\Exception $ex) {
                $errors = $ex->getMessage();
            }
        }

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'errors' => $errors,
            'posts' => $posts,
        ]);
    }
    /**
     * @Route("/blog/{id}/", name="blog_show", requirements={"id"=".*"})
     */
    public function show($id)
    {
        $request = Request::createFromGlobals();
        $index = $request->query->get('index');
        if(!isset($index))
        {
            $em = $this->getDoctrine()->getManager();

            $blog = $em->getRepository('App:Blog')->find($id);

            if (!$blog) {
                throw $this->createNotFoundException('Unable to find Blog post.');
            }
            //        $comments = $em->getRepository('App:Comment')
//            ->getCommentsForBlog($blog->getId());

            return $this->render('blog/templates/show.html.twig', array(
                'blog' => $blog,
//            'comments' => $comments
            ));
        }
        else
            {
                $feedIo = $this->container->get('feedio');
                $result = $feedIo->read($id);
                $result->getFeed()->getTitle();
                foreach ($result->getFeed() as $feed)
                {
                    if($index == 0){
                        $post = new Blog();
                        $post->setUrl($feed->getLink());
                        $post->setTitle($feed->getTitle());
                        $post->setText($feed->getDescription());
                        $post->setCreated($feed->getLastModified()->format(\DateTime::ATOM));
                        $post->setLocal('rss');
                        return $this->render('blog/templates/show.html.twig', array(
                            'blog' => $post,
//            'comments' => $comments
                        ));
                    }
                    $index --;
                }
                throw $this->createNotFoundException('Unable to find Blog post.');
            }
    }
}
