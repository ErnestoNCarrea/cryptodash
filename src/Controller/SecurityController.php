<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UsuarioPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UsuarioLoginType;
use App\Form\UsuarioRegistrationType;
use App\Form\NewPasswordType;
use App\Form\PasswordRequestType;
use App\Entity\Usuario;

class SecurityController extends AbstractController
{
    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        return $this->render(
            'logout.html.twig'
        );
    }

    /**
     * @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(UsuarioLoginType::class);
        return $this->render(
            'login.html.twig',
            [
                'form'          => $form->createView(),
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );
    }

    /**
     * @Route("/reset_password", name="reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer
    ) {
        $form = $this->createForm(PasswordRequestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $token = bin2hex(random_bytes(32));
            $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $email]);
            if ($usuario instanceof Usuario) {
                $usuario->setPasswordRequestToken($token);
                $entityManager->flush();

                // Enviar un mail al usuario con el token para resetear la contraseña
                $contenidoMensaje = '<h1>Su solicitud de cambio de contraseña</h1>
<p>Para restablecer su contraseña haga clic en el siguiente enlace:</p>
<p><a href="http://cryptodashg.cc/reset_password/confirm/' . $token . '" target="_blank">Restablecer contraseña</a><p>
<hr>
<p>Si el enlace no funciona, copie la siguiente dirección y péguela en su navegador:</p>
<p>http://cryptodash.vsign.com.ar/reset_password/confirm/' . $token . '</p>';
                $mensaje = (new \Swift_Message('Solicitud para restablecer su contraseña'))
                    ->setFrom(['cryptodash@vsign.com.ar' => 'CryptoDash'])
                    ->setTo($email)
                    ->setBody($contenidoMensaje)
                    ->addPart($contenidoMensaje, 'text/html');
                $mailer->send($mensaje);

                $this->addFlash('success', "Se envió un correo electrónico para restablecer la contraseña.");
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('reset-password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/reset_password/confirm/{token}", name="reset_password_confirm", methods={"GET", "POST"})
     */
    public function resetPasswordCheck(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UsuarioPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy(['passwordRequestToken' => $token]);
        if (!$token || !$usuario instanceof Usuario) {
            $this->addFlash('danger', "No se encontró el usuario.");
            return $this->redirectToRoute('reset_password');
        }
        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $password = $encoder->encodePassword($usuario, $plainPassword);
            $usuario->setPassword($password);
            $usuario->setPasswordRequestToken(null);
            $entityManager->flush();
            $token = new UsernamePasswordToken($usuario, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));
            $this->addFlash('success', "Se restableció la contraseña");
            return $this->redirectToRoute('home');
        }
        return $this->render('reset-password-confirm.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     */
    public function register(
        Request $request,
        UsuarioPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ) {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioRegistrationType::class, $usuario);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Buscar un usuario con el mismo e-mail
            $prevUsuario = $entityManager->getRepository('App\Entity\Usuario')->findBy(['email' => $usuario->getEmail()]);
            if ($prevUsuario) {
                $this->addFlash('success', 'Ya eixste una cuenta con esos datos.');
                return $this->render(
                    'register.html.twig',
                    [
                        'form' => $form->createView(),
                    ]
                );
            }
            $password = $encoder->encodePassword($usuario, $usuario->getPlainPassword());
            $usuario->setPassword($password);
            $entityManager->persist($usuario);
            $entityManager->flush();
            $this->addFlash('success', 'Su cuenta ha sido creada.');
            $token = new UsernamePasswordToken($usuario, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));
            return $this->redirectToRoute('home');
        }
        return $this->render(
            'register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
