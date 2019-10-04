<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_panel")
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function adminDashboard()
    {

        return new Response(
            '<html><body>Admin page!</body></html>'
        );
    }
}