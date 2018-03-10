<?php
//src/OF/UserBundle/Entity/User.php

namespace OF\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * User
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="OF\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
     /**
     * @Assert\File(maxSize="4000k")
     * @Assert\File(mimeTypes={ "application/pdf" })
     * Assert\Length(groups={"Registration", "Profile"})
     */
    private $cvFile;
  // for temporary storage
    private $tempCvPath;

     /**
     * @Assert\File(maxSize="2048k")
     * @Assert\Image(mimeTypesMessage="Please upload a valid image.")
     *
     * Assert\Length(groups={"Registration", "Profile"})
     */
    protected $profilePictureFile;

    // for temporary storage
    private $tempProfilePicturePath;
 
    /**
     * @ORM\Column(type="string", length=255, nullable=false)

     */
    protected $nom;
        /**
     * @ORM\Column(type="string", length=255, nullable=false)

     */
    protected $prenom;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)

     */
    protected $telephone;
    /**
     * @ORM\Column(type="integer",  nullable=true)

     */
    protected $sidenav;
        /**
     * @ORM\Column(type="string", length=255, nullable=true)

     */
    protected $rib;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)

     */
    protected $langue;
    /**
     * @ORM\Column(type="text", nullable=true)

     */
    protected $bio;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)

     */

    protected $profilePicturePath;

        /**
     * @ORM\Column(type="string", length=255, nullable=true)

     */

    protected $cvPath;

        public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->setSidenav(1);
        $this->setprofilePicturePath("default.png") ;
    }

    //////////////// CV ////////////////////////
    public function setCvFile(UploadedFile $file = null) {
        // set the value of the holder
        $this->cvFile       =   $file;
         // check if we have an old image path
        if (isset($this->cvPath)) {
            // store the old name to delete after the update
            $this->tempCvPath = $this->cvPath;
            $this->cvPath = 'absent';
        } else {
            $this->cvPath = 'initial';
        }

        return $this;
    }
         /**
     * Get the file used for cv picture uploads
     * 
     * @return UploadedFile
     */
    public function getCvFile() {

        return $this->cvFile;
    }



    /**
     * Set cvPath
     *
     * @param string $cvPath
     * @return User
     */
    public function setCvPath($cvPath)
    {
        $this->cvPath = $cvPath;

        return $this;
    }

    /**
     * Get cvPath
     *
     * @return string 
     */
    public function getCvPath()
    {
        return $this->cvPath;
    }

    /**
     * Get the absolute path of the cvPath
     */
    public function getCvAbsolutePath() {
        return null === $this->cvPath
            ? null
            : $this->getUploadRootDirCv().'/'.$this->cvPath;
    }

    /**
     * Get root directory for file uploads
     * 
     * @return string
     */
  
     protected function getUploadRootDirCv($type='cv')

    {

    // On retourne le chemin relatif vers l'image pour notre code PHP

    return __DIR__.'/../../../../web/'.$this->getUploadDirCv();

    }

    /**
     * Specifies where in the /web directory profile pic uploads are stored
     * 
     * @return string
     */
    protected function getUploadDirCv($type='cv') {
        // the type param is to change these methods at a later date for more file uploads
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/user/cv';
    }

    /**
     * Get the web path for the user
     * 
     * @return string
     */
    public function getWebCvPath() {

        return '/'.$this->getUploadDirCv().'/'.$this->getCvPath(); 
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadCv() {
        if (null !== $this->getCvFile()) {
            // a file was uploaded
            // generate a unique filename
            $filename = $this->generateRandomCvFilename();
            $this->setCvPath($filename.'.'.$this->getCvFile()->guessExtension());
        }
    }
     /**
     * Generates a 32 char long random filename
     * 
     * @return string
     */
    public function generateRandomCvFilename() {
        $count                  =   0;
        do {
            $random = random_bytes(16);
            $randomString = bin2hex($random);
            $count++;
        }
        while(file_exists($this->getUploadRootDirCv().'/'.$randomString.'.'.$this->getCvFile()->guessExtension()) && $count < 50);

        return $randomString;
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     * 
     * Upload the profile picture
     * 
     * @return mixed
     */
    public function uploadCv() {
        // check there is a profile pic to upload
        if ($this->getCvFile() === null) {
            return;
        }
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getCvFile()->move($this->getUploadRootDirCv(), $this->getCvPath());

        // check if we have an old image
        if (isset($this->tempCvPath) && file_exists($this->getUploadRootDirCv().'/'.$this->tempCvPath)) {
            // delete the old image
            unlink($this->getUploadRootDirCv().'/'.$this->tempCvPath);
            // clear the temp image path
            $this->tempCvPath = null;
        }
        $this->cvFile = null;
    }

     /**
     * @ORM\PostRemove()
     */
    public function removeCvFile()
    {
        if ($file = $this->getCvAbsolutePath() && file_exists($this->getCvAbsolutePath())) {
            unlink($this->getCvAbsolutePath());
        }
    }



    /////////// PROFILE PICTURE//////////////////

     /**
     * Sets the file used for profile picture uploads
     * 
     * @param UploadedFile $file
     * @return object
     */
    public function setProfilePictureFile(UploadedFile $file = null) {
        // set the value of the holder
        $this->profilePictureFile       =   $file;
         // check if we have an old image path
        if (isset($this->profilePicturePath)) {
            // store the old name to delete after the update
            $this->tempProfilePicturePath = $this->profilePicturePath;
            $this->profilePicturePath = 'absent';
        } else {
            $this->profilePicturePath = 'initial';
        }

        return $this;
    }

     /**
     * Get the file used for profile picture uploads
     * 
     * @return UploadedFile
     */
    public function getProfilePictureFile() {

        return $this->profilePictureFile;
    }




 	/**
     * Set profilePicturePath
     *
     * @param string $profilePicturePath
     * @return User
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;

        return $this;
    }

    /**
     * Get profilePicturePath
     *
     * @return string 
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * Get the absolute path of the profilePicturePath
     */
    public function getProfilePictureAbsolutePath() {
        return null === $this->profilePicturePath
            ? null
            : $this->getUploadRootDir().'/'.$this->profilePicturePath;
    }

    /**
     * Get root directory for file uploads
     * 
     * @return string
     */
  
     protected function getUploadRootDir($type='profilePicture')

  	{

    // On retourne le chemin relatif vers l'image pour notre code PHP

    return __DIR__.'/../../../../web/'.$this->getUploadDir();

  	}

    /**
     * Specifies where in the /web directory profile pic uploads are stored
     * 
     * @return string
     */
    protected function getUploadDir($type='profilePicture') {
        // the type param is to change these methods at a later date for more file uploads
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/user/profilepics';
    }

    /**
     * Get the web path for the user
     * 
     * @return string
     */
    public function getWebProfilePicturePath() {

        return '/'.$this->getUploadDir().'/'.$this->getProfilePicturePath(); 
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadProfilePicture() {
        if (null !== $this->getProfilePictureFile()) {
            // a file was uploaded
            // generate a unique filename
            $filename = $this->generateRandomProfilePictureFilename();
            $this->setProfilePicturePath($filename.'.'.$this->getProfilePictureFile()->guessExtension());
        }
    }
     /**
     * Generates a 32 char long random filename
     * 
     * @return string
     */
    public function generateRandomProfilePictureFilename() {
        $count                  =   0;
        do {
            $random = random_bytes(16);
            $randomString = bin2hex($random);
            $count++;
        }
        while(file_exists($this->getUploadRootDir().'/'.$randomString.'.'.$this->getProfilePictureFile()->guessExtension()) && $count < 50);

        return $randomString;
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     * 
     * Upload the profile picture
     * 
     * @return mixed
     */
    public function uploadProfilePicture() {
        // check there is a profile pic to upload
        if ($this->getProfilePictureFile() === null) {
            return;
        }
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getProfilePictureFile()->move($this->getUploadRootDir(), $this->getProfilePicturePath());

        // check if we have an old image
        if (isset($this->tempProfilePicturePath) && file_exists($this->getUploadRootDir().'/'.$this->tempProfilePicturePath)) {
            // delete the old image
             if ($this->tempProfilePicturePath != "default.png"){
                unlink($this->getUploadRootDir().'/'.$this->tempProfilePicturePath);
            }
            // clear the temp image path
            $this->tempProfilePicturePath = null;
        }
        $this->profilePictureFile = null;
    }

     /**
     * @ORM\PostRemove()
     */
    public function removeProfilePictureFile()
    {
        if ($this->profilePicturePath != "default.png"){
            if ($file = $this->getProfilePictureAbsolutePath() && file_exists($this->getProfilePictureAbsolutePath())) {
                //unlink($this->getProfilePictureAbsolutePath());
            }
        }
    }


    


    



    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set rib
     *
     * @param string $rib
     *
     * @return User
     */
    public function setRib($rib)
    {
        $this->rib = $rib;

        return $this;
    }

    /**
     * Get rib
     *
     * @return string
     */
    public function getRib()
    {
        return $this->rib;
    }

    /**
     * Set langue
     *
     * @param string $langue
     *
     * @return User
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return string
     */
    public function getLangue()
    {
        return $this->langue;
    }

   
    public function setSidenav($sidenav)
    {
        $this->sidenav = $sidenav;

        return $this;
    }


    public function getSidenav()
    {
        return $this->sidenav;
    }

    /**
     * Set bio
     *
     * @param string $bio
     *
     * @return User
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

}
