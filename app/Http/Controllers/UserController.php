<?php

namespace App\Http\Controllers;

use App\Exceptions\LdapAccessDeniedException;
use App\Exceptions\LdapEntityNotFountException;
use App\Helpers\AclHelper;
use App\Helpers\LdapHelper;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function getUser(): User|Authenticatable|null
    {
        return Auth::check() ? Auth::user() : null;
    }

    /**
     * @throws LdapEntityNotFountException
     * @throws AuthenticationException
     * @throws LdapAccessDeniedException
     */
    public function authUser(Request $request)
    {
        $credentials = [
            'samaccountname' => $request->get('username'),
            'password' => $request->get('password'),
        ];
        if (LdapHelper::isHelpdeskUser($request->get('username'))) {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $user->is_admin = LdapHelper::isHelpdeskAdmin($user->username);
                $user->is_super_admin = AclHelper::isSuperAdmin();
                $user->save();
                return $user;
            } else {
                throw new AuthenticationException();
            }
        } else {
            throw new LdapAccessDeniedException();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }

    /**
     * @param Request $request
     * @return User|Authenticatable|null
     */
    public function editProfile(Request $request): User|Authenticatable|null
    {
        $user = Auth::user();
        $user->update($request->all());
        return $user;
    }

    public function updateAvatar(Request $request)
    {
        $user = $this->getUser();
        $file = $request->file('avatar');
        $coordinates = $request->get('coordinates');
        $path = '/private/avatars/' . Auth::id();
        $original = $path . DIRECTORY_SEPARATOR . 'original.jpg';
        $thumb = $path . DIRECTORY_SEPARATOR . 'thumb.jpg';
        if (!Storage::directoryExists($path)) {
            Storage::makeDirectory($path);
        }
        if (self::cropPhoto($file, $coordinates, [
            'original' => Storage::path($original),
            'thumb' => Storage::path($thumb),
        ])) {
            $user->photo = '/avatars/' . $user->id;
            $user->save();
        }
        return $user;
    }

    public function getAvatar(int $id, int $size = 300)
    {
        $path = '/private/avatars/' . $id;
        $thumb = $path . DIRECTORY_SEPARATOR . 'thumb.jpg';
        return Image::make(Storage::path($thumb))->widen($size)->response('jpg');
    }

    public static function cropPhoto(UploadedFile $file, array $coords, array $path)
    {
        $w = $coords['width'];
        $h = $coords['height'];
        $x = $coords['left'];
        $y = $coords['top'];
        $img = Image::make($file);
        $imgWidth = $img->getWidth();
        $imgHeight = $img->getHeight();
        $save_path = $path['original'];
        $save_thumb_path = $path['thumb'];
        $r = Image::make($file)->resize($imgWidth, $imgHeight)->save($save_path);
        if (Image::make($r)->crop($w, $h, $x, $y)->save($save_thumb_path)) {
            return true;
        }
        return false;
    }

    public function searchUsers($term)
    {
        return User::select(['id', 'photo', 'email', 'firstname', 'lastname', 'position', 'department', 'organization'])->where(function (Builder $builder) use ($term) {
            $builder->orWhere('email', 'LIKE', $term . "%");
            $builder->orWhere('firstname', 'LIKE', $term . "%");
            $builder->orWhere('lastname', 'LIKE', $term . "%");
        })->get();
    }
}
